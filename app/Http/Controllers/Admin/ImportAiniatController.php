<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\ImportAiniat;
use App\Models\Product;
use App\Models\Provider;
use App\Models\BuyedProduct;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Exports\ImportAiniatExport;
use Maatwebsite\Excel\Facades\Excel;

class ImportAiniatController extends Controller
{

  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  // index
  public function index()
  {
    $page = config('app.page');
    $import_ainiats = ImportAiniat::select('id', 'number', 'date_created', 'notes', 'provider_id', 'user_id')->with(['provider:id,name','user:id,name'])->orderBy('id', 'DESC')->paginate($page);

    return view('admin.import_ainiat.index', compact('import_ainiats'));
  }

  // create
  public function create()
  {
    $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
    $products = DB::select('SELECT id, name, quantity FROM products ORDER BY id DESC');

    return view('admin.import_ainiat.create', compact('providers', 'products'));
  }

  // store
  public function store(Request $request)
  {

    $provider_id = $request['provider_id'];
    $user_id = Auth::user()->id;

    DB::beginTransaction();
    try {

      $import_ainiat = new ImportAiniat;
      $import_ainiat->number = $request['number'];
      $import_ainiat->date_created = $request['date_created'];
      $import_ainiat->provider_id = $provider_id;
      $import_ainiat->user_id = $user_id;
      $import_ainiat->notes = $request['notes'] ?? 'لا يوجد';
      $import_ainiat->save();

      $tblArray = explode(',', $request['tbl']);

      if ($tblArray == null) {
        DB::rollBack();
        return redirect('/import_ainiats');
      }

      for ($i = 0; $i < count($tblArray) / 2; $i++) {

        $buyed_product = new BuyedProduct;
        $buyed_product->product_id = $tblArray[$i * 2 + 0];
        $buyed_product->quantity = $tblArray[$i * 2 + 1];
        $buyed_product->import_ainiat_id = $import_ainiat->id;
        $buyed_product->save();

        $product = Product::where('id', $tblArray[$i * 2 + 0])->first();

        Product::where('id', $tblArray[$i * 2 + 0])->update([
          'quantity' => $product->quantity + $tblArray[$i * 2 + 1],
          'original_quantity' => $product->original_quantity + $tblArray[$i * 2 + 1],
          'status' => true
        ]);
      }

      DB::commit();
      return redirect('/import_ainiats')->with('success', 'تم استيراد العينيات بنجاح');
    } catch (\Exception $e) {
      DB::rollBack();
      // dd($e->getMessage());
      return redirect('/import_ainiat/create')->with('error', $e->getMessage());
    }
  }

  // show
  public function show(Request $request)
  {
    $id = $request['id'];
    $bill = ImportAiniat::select('id', 'number', 'date_created', 'provider_id', 'notes')->with('buyed_product:id,product_id,quantity,import_ainiat_id')->with('provider:id,name')->with('buyed_product.product:id,name')->where('id', $id)->first();

    if ($bill != null) {
      $bill_data = view('includes.bill_data', compact('bill'))->render();
      return response()->json(['bill_data' => $bill_data]);
    } else {
      return response(['status' => 'error']);
    }
  }

  // edit
  public function edit($id)
  {
    $import_ainiat = ImportAiniat::select('id', 'number', 'date_created', 'provider_id', 'notes')->with('buyed_product:id,product_id,quantity,import_ainiat_id')->where('id', $id)->first();
    $products = DB::select('SELECT id, name, quantity FROM products ORDER BY id DESC');
    return view('admin.import_ainiat.edit', compact('import_ainiat', 'products'));
  }

  // update
  public function update(Request $request, $id)
  {

    $import_ainiat = ImportAiniat::where('id', $id)->first();

    if ($request['tbl'] == null) {
      return redirect('/import_ainiats');
    } else {

      DB::beginTransaction();
      try {
        $tblArray = explode(',', $request['tbl']);
        if ($request->tbl != null) {
          for ($i = 0; $i < count($tblArray) / 2; $i++) {

            $buyed_product = new BuyedProduct;
            $buyed_product->product_id = $tblArray[$i * 2 + 0];
            $buyed_product->quantity = $tblArray[$i * 2 + 1];
            $buyed_product->import_ainiat_id = $id;
            $buyed_product->save();

            $product = Product::where('id', $tblArray[$i * 2 + 0])->first();

            Product::where('id', $tblArray[$i * 2 + 0])->update([
              'quantity' => $product->quantity + $tblArray[$i * 2 + 1],
              'original_quantity' => $product->original_quantity + $tblArray[$i * 2 + 1],
              'status' => true
            ]);
          }
        }

        $import_ainiat->update(
          ['notes' => $request['notes']]
        );

        DB::commit();
        return redirect('/import_ainiats')->with('success', 'تم تحديث الفاتورة بنجاح!');
      } catch (Exception $e) {
        DB::rollBack();
        return redirect('/import_ainiat/edit/' . $id)->with('error', $e->getMessage());
      }
    }
  }

  // delete product
  public function delete_product($id)
  {

    DB::beginTransaction();
    try {

      $buyed_product = BuyedProduct::where('id', $id)->with('import_ainiat')->first();
      $product = Product::where('id', $buyed_product->product_id)->first();

      if (($product->quantity - $buyed_product->quantity) == 0) {
        Product::where('id', $buyed_product->product_id)->update([
          'quantity' => $product->quantity - $buyed_product->quantity,
          'original_quantity' => $product->original_quantity - $buyed_product->quantity,
          'status' => false
        ]);
      } else {
        Product::where('id', $buyed_product->product_id)->update([
          'quantity' => $product->quantity - $buyed_product->quantity,
          'original_quantity' => $product->original_quantity - $buyed_product->quantity,
          'status' => true
        ]);
      }

      $buyed_product->delete();

      DB::commit();
      return redirect('/import_ainiat/edit/' . $buyed_product->import_ainiat_id);
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/import_ainiats')->with('error', 'Error: ' . $e->getMessage());
    }
  }

  // delete
  public function delete(Request $request)
  {
    // delete buy bill
    $import_ainiat = ImportAiniat::where('id', $request['id'])->first();
    if ($import_ainiat != null) {

      $import_ainiat->delete();
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'error']);
    }
  }

  // to pdf
  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');
    $import_ainiats = ImportAiniat::select('id', 'number', 'date_created', 'notes', 'provider_id')->with('provider:id,name')->whereRaw('date_created >= ? AND date_created <= ?', [$from, $to])->orderBy('id', 'DESC')->get();

    $i = 1;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل عينيات واردة</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">#</th>
            <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="25%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="25%" bgcolor="#eee">المستفيد</th>
            <th width="25%" bgcolor="#eee">الملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($import_ainiats as $import_ainiat) {
      $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="20%">' . $import_ainiat->number . '</td>
              <td width="25%">' . $import_ainiat->date_created . '</td>
              <td width="25%">' . $import_ainiat->provider->name . '</td>
              <td width="25%">' . $import_ainiat->notes . '</td>
            </tr>';
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل عينيات واردة');
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
    $directoryPath = storage_path('app/public/pdf/عينيات واردة' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }
    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف عينيات واردة_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    return response()->json(['status' => 'success']);
  }

  // to xlsx
  public function to_xlsx(Request $request)
  {
    // dd(date($request->from . ' 00:00:00'));
    $fileName = 'كشف عينيات واردة_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/عينيات واردة' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new ImportAiniatExport(), 'xlsx/عينيات واردة' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
