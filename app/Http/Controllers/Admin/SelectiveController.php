<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Selective;
use App\Models\Customer;
use App\Models\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Exports\SelectiveExport;
use App\Imports\SelectiveImport;

use Maatwebsite\Excel\Facades\Excel;

class SelectiveController extends Controller
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
    $customers = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'notes', 'status', 'mosque_id', 'created_at')
      ->where('status', 0)
      ->with('mosque:id,name')
      ->orderBy('id', 'DESC')
      ->paginate($page);

    $products = Product::select('id', 'name')->get();

    if ($request->ajax()) {
      $table = view('admin.selective.table', compact('customers'))->render();
      return response()->json(['table' => $table]);
    } else {
      $pages = ceil(Selective::count() / $page);
      return view('admin.selective.index', compact('customers', 'pages', 'products'));
    }
  }

  // store
  public function store(Request $request)
  {
    $user_id = Auth::user()->id;
    $product_id = $request->product_id;

    $file_attachment = $request->file('file_attachment');

    $xlsx_data = null;

    if ($file_attachment != null && $request->product_id != null) {
      // Read the xlsx file
      $xlsx_data = Excel::toArray(new SelectiveImport, $file_attachment);

      // Assuming we have only one sheet in the Excel file, so we'll take the first sheet
      $sheetData = $xlsx_data[0];

      // Start transaction
      DB::beginTransaction();
      try {
        // Loop through the rows, skip the first row (headers)
        for ($i = 1; $i < count($sheetData); $i++) {
          $row = $sheetData[$i];
          // Ensure the row is valid (not empty)
          if ($row[0] != null) {
            // check if customer exists then update it if not then add new one
            $customer = Customer::where('identity', $row[1])->first();
            // dd($customer);
            $selective = Selective::where('customer_id', $customer->id)
              ->where('product_id', $product_id)
              ->where('status', 0)
              ->first();

            if ($customer != null && $selective == null) {

              $customer->update([
                'status' => 0,
              ]);

              $selective = new Selective();
              $selective->user_id = $user_id;
              $selective->customer_id = $customer->id;
              $selective->product_id = $product_id;
              $selective->status = 0;
              $selective->save();
            }
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة المرشحين بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        // return dd($e->getMessage());  // For debugging
        // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ المستفيدون']);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }
    } else {
      return response()->json(['status' => 'error', 'message' => 'حدث خطا اثناء اضافة المرشح!']);
    }
  }

  // to pdf
  public function to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');

    $customers = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'notes', 'status', 'mosque_id', 'created_at')
      ->with('mosque:id,name')
      ->whereBetween('created_at', [$from, $to])
      ->where('status', false)
      ->orderBy('id', 'DESC')
      ->get();

    $i = 1;
    $total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل المرشحون</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">الرقم</th>
            <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="15%" bgcolor="#eee">الاسم</th>
            <th width="10%" bgcolor="#eee">رقم الهوية</th>
            <th width="10%" bgcolor="#eee">رقم الجوال</th>
            <th width="5%" bgcolor="#eee">عدد افراد الاسرة</th>
            <th width="10%" bgcolor="#eee">المسجد</th>
            <th width="10%" bgcolor="#eee">الحالة</th>
            <th width="20%" bgcolor="#eee">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($customers as $customer) {
      $status = '';
      if ($customer->status == 1) {
        $status = 'مستفيد';
      } else {
        $status = 'مرشح';
      }
      $mosque = '';
      if ($customer->mosque) {
        $mosque = $customer->mosque->name;
      }
      $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="15%">' . $customer->created_at . '</td>
              <td width="15%">' . $customer->name . '</td>
              <td width="10%">' . $customer->identity . '</td>
              <td width="10%">' . $customer->phone . '</td>
              <td width="5%">' . $customer->family_number . '</td>
              <td width="10%">' . $mosque . '</td>
              <td width="10%">' . $status . '</td>
              <td width="20%">' . $customer->notes . '</td>
            </tr>';
      $total += $customer->balance;
      $i++;
    }
    if ($total < 0) {
      $total = $total . '<span>&#8362;&#160;</span> - مدين -';
    } elseif ($total > 0) {
      $total = $total . '<span>&#8362;&#160;</span> - دائن -';
    } else {
      $total = $total;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل المرشحون');
    PDF::SetAuthor($by);
    // set some language dependent data:
    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $lg['w_page'] = 'page';
    PDF::SetPageOrientation('L', 'P');
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
    $directoryPath = storage_path('app/public/pdf/المرشحون' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف المرشحون_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('all_customers_' . date('ymdhis') . '.pdf', 'I');
    return response()->json(['status' => 'success']);
  }

  // to xlsx
  public function to_xlsx(Request $request)
  {
    // dd(date($request->from . ' 00:00:00'));
    $fileName = 'كشف المرشحين_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/المرشحون' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new SelectiveExport(), 'xlsx/المرشحون' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
