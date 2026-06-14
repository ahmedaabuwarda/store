<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Product;
use App\Models\Customer;
use App\Models\ExportAiniat;
use App\Models\Selective;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Imports\ExportAiniatImport;
use App\Exports\ExportAiniatExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportAiniatController extends Controller
{

  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  // index
  public function index(Request $request)
  {

    $page = config('app.page');
    $export_ainiats = ExportAiniat::select('id', 'user_id', 'number', 'date_created', 'notes')
    ->with(['selective', 'selective.product', 'user'])
    ->orderBy('id', 'DESC')
    ->paginate($page);
    // dd($export_ainiats->toArray());
    $products = Product::select('id', 'name')->get();

    if ($request->ajax()) {
      $table = view('admin.export_ainiat.table', compact('export_ainiats'))->render();
      return response()->json(['status' => 'success', 'table' => $table]);
    } else {
      return view('admin.export_ainiat.index', compact('export_ainiats', 'products'));
    }
  }

  // public function create()
  // {
  //   $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
  //   $customers = DB::select('SELECT id, name FROM customers ORDER BY id DESC');
  //   $workers = DB::select('SELECT id, name FROM workers ORDER BY id DESC');
  //   $products = DB::select('SELECT id, name, quantity FROM products ORDER BY id DESC');
  //   $boxes = Box::select('id', 'name')->get();

  //   return view('admin.export_ainiat.create', compact('providers', 'customers', 'workers', 'products', 'boxes'));
  // }

  public function store(Request $request)
  {
    $product_id = $request['product_id'];
    $date_created = $request['date_created'];
    $notes = $request['notes'];
    $user_id = Auth::user()->id;
    //dd($request->toArray());

    $file_attachment = $request->file('file_attachment');

    $xlsx_data = null;

    if ($file_attachment != null && $request->product_id != null) {
      // Read the xlsx file
      $xlsx_data = Excel::toArray(new ExportAiniatImport, $file_attachment);

      // Assuming we have only one sheet in the Excel file, so we'll take the first sheet
      $sheetData = $xlsx_data[0];

      // Start transaction
      DB::beginTransaction();
      try {

        $export_ainiat = new ExportAiniat();
        $export_ainiat->user_id = $user_id;
        $export_ainiat->number = date('YmdHis');
        $export_ainiat->date_created = $date_created;
        $export_ainiat->notes = $notes ?? 'لا يوجد';
        $export_ainiat->save();
        // dd($request->toArray());
        $product = Product::where('id', $product_id)->first();

        $product->update([
          'quantity' => $product->quantity - (count($sheetData) - 2),
        ]);

        // Loop through the rows, skip the first row (headers)
        for ($i = 1; $i < count($sheetData); $i++) {
          $row = $sheetData[$i];
          // Ensure the row is valid (not empty)
          if ($row[0] != null) {
            // check if customer exists then update it if not then add new one
            $customer = Customer::where('identity', $row[1])->first();

            $selective = Selective::where('customer_id', $customer->id)
              ->where('product_id', $product_id)
              ->where('status', 0)
              ->first();

            // if ther is no selectives create new selective for that customer
            if ($selective == null) {
              $selective = new Selective();
              $selective->user_id = $user_id;
              $selective->export_ainiat_number = $export_ainiat->number;
              $selective->customer_id = $customer->id;
              $selective->product_id = $product_id;
              $selective->status = 1;
              $selective->save();
            } else {
              $selective->update([
                'status' => 1,
              ]);
            }

            // if this customer has no selectives then update its status to 1
            $selective = Selective::where('customer_id', $customer->id)->where('status', 0)->get();
            if ($selective->count() == 0) {
              Customer::where('id', $customer->id)->update(['status' => 1]);
            }
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة الستفيدون بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ الزبائن']);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }
    }
    return redirect('/export_ainiats')->with('success', 'بعض الحقول مطلوبة!');
  }

  // public function show(Request $request)
  // {
  //   $id = $request['id'];
  //   $bill = ExportAiniat::select('id', 'number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'total_balance', 'paid_balance', 'remaining_balance', 'expense', 'notes', 'total_profit')->with('sold_product:id,product_id,quantity,sell_price,total_price,profit,export_ainiat_id')->with('sold_product.product:id,name')->where('id', $id)->first();
  //   if ($bill != null) {
  //     $bill_data = view('includes.bill_data', compact('bill'))->render();
  //     return response()->json(['bill_data' => $bill_data]);
  //   } else {
  //     return response(['status' => 'error']);
  //   }
  // }

  public function edit($id)
  {
    $export_ainiat = ExportAiniat::select('id', 'number', 'date_created', 'notes')
      ->with([
        'selective',
        'selective.product',
        'selective.customer',
      ])
      ->where('id', $id)
      ->first();
    $customers = Customer::select('id', 'name')->get();
    return view('admin.export_ainiat.edit', compact('export_ainiat', 'customers'));
  }

  public function update(Request $request, $id)
  {

    $notes = $request['notes'];

    $export_ainiat = ExportAiniat::where('id', $id)->first();

    DB::beginTransaction();
    try {

      $export_ainiat->update([
        'notes' => $notes,
      ]);

      DB::commit();
      return redirect('/export_ainiat/edit/' . $id)->with('success', 'تم تحديث الفاتورة بنجاح');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/export_ainiats')->with('error', $e->getMessage());
    }
  }

  // public function delete_product($id)
  // {

  //   DB::beginTransaction();
  //   try {

  //     $sold_product = SoldProduct::where('id', $id)->with('export_ainiat')->first();
  //     $product = Product::where('id', $sold_product->product_id)->first();

  //     $profit = ($sold_product->sell_price * $sold_product->quantity) - ($sold_product->buy_price * $sold_product->quantity);

  //     $export_ainiat = ExportAiniat::where('id', $sold_product->export_ainiat_id)->update([
  //       'total_balance' => $sold_product->export_ainiat->total_balance - $sold_product->total_price,
  //       'paid_balance' => $sold_product->export_ainiat->paid_balance - $sold_product->total_price,
  //       'total_profit' => $sold_product->export_ainiat->total_profit - $profit
  //     ]);

  //     Product::where('id', $sold_product->product_id)->update([
  //       'quantity' => $product->quantity + $sold_product->quantity,
  //       'status' => true
  //     ]);

  //     $sold_product->delete();

  //     DB::commit();
  //     return redirect('/export_ainiat/edit/' . $sold_product->export_ainiat_id);
  //   } catch (\Exception $e) {
  //     DB::rollBack();
  //     return redirect('/export_ainiats')->with('error', 'Error: ' . $e->getMessage());
  //   }
  // }

  // delete
  public function delete(Request $request)
  {
    // delete export_ainiat
    $export_ainiat = ExportAiniat::where('id', $request['id'])->first();
    if ($export_ainiat != null) {

      $export_ainiat->delete();
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'error']);
    }
  }

  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');

    $export_ainiats = ExportAiniat::select('id', 'number', 'date_created', 'notes', 'user_id')
      ->with(['selective', 'selective.product', 'user'])
      ->whereRaw('date_created >= ? AND date_created <= ?', [$from, $to])
      ->orderBy('id', 'DESC')
      ->get();

    $i = 1;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل عينيات صادرة</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="20%" bgcolor="#eee">#</th>
            <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">العينية</th>
            <th width="10%" bgcolor="#eee">بواسطة</th>
            <th width="10%" bgcolor="#eee">الملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($export_ainiats as $export_ainiat) {
      $table_content .= '<tr>
              <td width="20%">' . $i . '</td>
              <td width="20%">' . $export_ainiat->number . '</td>
              <td width="20%">' . $export_ainiat->date_created . '</td>
              <td width="20%">' . $export_ainiat->selective[0]->product->name . '</td>
              <td width="10%">' . $export_ainiat->user->name . '</td>
              <td width="10%">' . $export_ainiat->notes . '</td>
            </tr>';
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل عينيات صادرة');
    PDF::SetAuthor($by);
    // set some language dependent data:
    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $lg['w_page'] = 'page';
    // set some language-dependent strings (optional)
    PDF::setLanguageArray($lg);
    // set font
    PDF::SetFont('aealarabiya', '', 11);
    // set margins
    PDF::SetMargins(PDF_MARGIN_LEFT, /*PDF_MARGIN_TOP,*/ PDF_MARGIN_RIGHT);
    PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
    PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

    PDF::AddPage();
    PDF::writeHTML($content);
    PDF::SetFont('freeserif', '', 11);
    PDF::writeHTML($table_content);

    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/عينيات صادرة' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }
    // Save the file to the storage folder
    $filePath = $directoryPath . '/' . 'كشف عينيات صادرة' . '_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('provider_kashf_hisab_' . date('ymdhis') . '.pdf', 'D');
    return response()->json(['status' => 'success']);
  }


  // to xlsx
  public function to_xlsx(Request $request)
  {
    // dd(date($request->from . ' 00:00:00'));
    $fileName = 'كشف عينيات صادرة_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/عينيات صادرة' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new ExportAiniatExport(), 'xlsx/عينيات صادرة' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
