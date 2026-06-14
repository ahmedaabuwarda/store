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
use App\Models\Sanadat_Qapd;
use App\Models\Sanadat_Sarf;
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
      $pages = $customers->lastPage();
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
        // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ الزبائن']);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }
    } else {
      return response()->json(['status' => 'error', 'message' => 'حدث خطا اثناء اضافة المرشح!']);
    }
  }

  // delete
  public function delete(Request $request, $id)
  {
    $selective = Selective::where('id', $id)->with('export_ainiat:id,number')->first();
    $product = Product::where('id', $selective->product_id)->first();
    // Start transaction
    DB::beginTransaction();
    try {

      $product->update([
        'quantity' => $product->quantity + 1,
      ]);

      $selective->delete();

      // Commit the transaction
      DB::commit();
      return redirect('/export_ainiat/edit/' . $selective->export_ainiat->id)->with(['success' => 'تم حذف العينية بنجاح']);
    } catch (Exception $e) {
      // Rollback the transaction in case of error
      DB::rollback();
      // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ الزبائن']);
      return redirect('/export_ainiat/edit/' . $selective->export_ainiat->id)->with(['error', $e->getMessage()]);
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
        $status = 'زبون';
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

  // kashf hesap
  public function kashf_to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');
    $id = $request->id;

    // $customer = DB::select('SELECT name, balance, phone, identity, family_number FROM customers WHERE id = :id', ['id' => $id]);

    $customer_sarf = Sanadat_Sarf::select('id', 'date_created', 'number', 'balance', 'notes', 'customer_id', 'box_id', 'user_id')
      ->with(['customer', 'box', 'user'])
      ->whereBetween('date_created', [$from, $to])
      ->where('customer_id', $id)
      ->orderBy('id', 'DESC')
      ->get();

    $customer_qapd = Sanadat_Qapd::select('id', 'date_created', 'number', 'balance', 'notes', 'customer_id', 'box_id', 'user_id')
      ->with(['customer', 'box', 'user'])
      ->whereBetween('date_created', [$from, $to])
      ->where('customer_id', $id)
      ->orderBy('id', 'DESC')
      ->get();

    $customer_buy = DB::select('SELECT import_ainiats.date_created, import_ainiats.number, import_ainiats.paid_balance, import_ainiats.notes, import_ainiats.remaining_balance FROM customers, import_ainiats WHERE customers.id = import_ainiats.customer_id AND customers.id = :id AND import_ainiats.date_created >= :from AND import_ainiats.date_created <= :to ORDER BY import_ainiats.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

    $customer_sell = DB::select('SELECT export_ainiats.date_created, export_ainiats.number, export_ainiats.paid_balance, export_ainiats.notes, export_ainiats.remaining_balance FROM customers, export_ainiats WHERE customers.id = export_ainiats.customer_id AND customers.id = :id AND export_ainiats.date_created >= :from AND export_ainiats.date_created <= :to ORDER BY export_ainiats.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

    $customer_ainiat = Customer::with([
      'selective.product:id,name',
      'selective.user:id,name',
      'export_ainiat:customer_id,number',
    ])
      // ->whereBetween('date_created', [$from, $to])
      ->where('id', $id)
      ->first();
    // dd($customer_ainiat->toArray());

    $i = 1;
    $sarf_total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;
    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4>
      <h1 align="center">كشف حساب</h1>
      </br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br><p>الاسم: ' . $customer_ainiat->name . ' - زبون' . '&#160;&#160;رقم الهوية: ' . $customer_ainiat->identity . '&#160;&#160;رقم الجوال: ' . $customer_ainiat->phone . '&#160;&#160;عدد افراد الاسرة: ' . $customer_ainiat->family_number . '</p></br>';

    // sanadat sarf
    $sarf_table = '<h2>سندات الصرف</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th width="5%" bgcolor="#eee">#</th>
              <th width="15%" bgcolor="#eee">رقم السند</th>
              <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
              <th width="10%" bgcolor="#eee">المبلغ</th>
              <th width="20%" bgcolor="#eee">لصنندوق</th>
              <th width="15%" bgcolor="#eee">بواسطة</th>
              <th width="20%" bgcolor="#eee">الملاحظات</th>
            </tr>
          </thead>
          <tbody>';
    foreach ($customer_sarf as $sanadat_sarf) {
      $sarf_table .= '<tr>
                <td width="5%">' . $i . '</td>
                <td width="15%">' . $sanadat_sarf->number . '</td>
                <td width="15%">' . $sanadat_sarf->date_created . '</td>
                <td width="10%">' . $sanadat_sarf->balance . ' ' . $sanadat_sarf->box->currency->symbol . '</td>
                <td width="20%">' . $sanadat_sarf->box->name . '</td>
                <td width="15%">' . $sanadat_sarf->user->name . '</td>
                <td width="20%">' . $sanadat_sarf->notes . '</td>
              </tr>';
      $sarf_total += $sanadat_sarf->balance;
      $i++;
    }

    $sarf_table .= '</tbody></table>';

    // sanadat qapd
    $i = 1;
    $qapd_total = 0;
    $qapd_table = '<h2>سندات القبض</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th width="5%" bgcolor="#eee">#</th>
              <th width="15%" bgcolor="#eee">رقم السند</th>
              <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
              <th width="10%" bgcolor="#eee">المبلغ</th>
              <th width="20%" bgcolor="#eee">لصنندوق</th>
              <th width="15%" bgcolor="#eee">بواسطة</th>
              <th width="20%" bgcolor="#eee">الملاحظات</th>
            </tr>
          </thead>
          <tbody>';
    foreach ($customer_qapd as $sanadat_qapd) {
      $qapd_table .= '<tr>
                <td width="5%">' . $i . '</td>
                <td width="15%">' . $sanadat_qapd->number . '</td>
                <td width="15%">' . $sanadat_qapd->date_created . '</td>
                <td width="10%">' . $sanadat_qapd->balance . ' ' . $sanadat_qapd->box->currency->symbol . '</td>
                <td width="15%">' . $sanadat_qapd->box->name . '</td>
                <td width="15%">' . $sanadat_qapd->user->name . '</td>
                <td width="20%">' . $sanadat_qapd->notes . '</td>
              </tr>';
      $qapd_total += $sanadat_qapd->balance;
      $i++;
    }

    $qapd_table .= '</tbody></table>';

    // ainiat
    $i = 1;
    $ainiat_table = '<h2>العينيات</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th width="20%" bgcolor="#eee">#</th>
              <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
              <th width="20%" bgcolor="#eee">الاسم</th>
              <th width="20%" bgcolor="#eee">بواسطة</th>
              <th width="20%" bgcolor="#eee">الحالة</th>
            </tr>
          </thead>
          <tbody>';
    foreach ($customer_ainiat->selective as $selective) {
      $status = $selective->status == 0 ? 'مرشج' : 'زبون';
      $ainiat_table .= '<tr>
                <td width="20%">' . $i . '</td>
                <td width="20%">' . $selective->created_at . '</td>
                <td width="20%">' . $selective->product->name . '</td>
                <td width="20%">' . $selective->user->name . '</td>
                <td width="20%">' . $status . '</td>
              </tr>';
      $i++;
    }

    $ainiat_table .= '</tbody></table>';

    // import ainiat
    $i = 1;
    $buy_total = 0;
    $buy_table = '<h2>عينيات واردة</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th width="5%" bgcolor="#eee">#</th>
              <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
              <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
              <th width="15%" bgcolor="#eee">المبلغ المدفوع</th>
              <th width="20%" bgcolor="#eee">المبلغ المتبقي</th>
              <th width="20%" bgcolor="#eee">الملاحظات</th>
            </tr>
          </thead>
          <tbody>';
    foreach ($customer_buy as $import_ainiat) {
      $remaining = '';
      if ($import_ainiat->remaining_balance > 0) {
        $remaining = $import_ainiat->remaining_balance . '<span>&#8362;&#160;</span> - دائن -';
      } else if ($import_ainiat->remaining_balance < 0) {
        $remaining = $import_ainiat->remaining_balance . '<span>&#8362;&#160;</span> - مدين -';
      } else {
        $remaining = $remaining = $import_ainiat->remaining_balance . '<span>&#8362;&#160;</span>';
      }
      $buy_table .= '<tr>
                <td width="5%">' . $i . '</td>
                <td width="20%">' . $import_ainiat->number . '</td>
                <td width="20%">' . $import_ainiat->date_created . '</td>
                <td width="15%">' . $import_ainiat->paid_balance . '<span>&#8362;&#160;</span></td>
                <td width="20%">' . $remaining . '</td>
                <td width="20%">' . $import_ainiat->notes . '</td>
              </tr>';
      $buy_total += $import_ainiat->remaining_balance;
      $i++;
    }
    if ($buy_total > 0) {
      $buy_total = $buy_total . '<span>&#8362;&#160;</span> - دائن -';
    } else if ($buy_total < 0) {
      $buy_total = $buy_total . '<span>&#8362;&#160;</span> - مدين -';
    } else {
      $buy_total = $buy_total . '<span>&#8362;&#160;</span>';
    }

    $buy_table .= '</tbody></table>';

    // export ainiat
    $i = 1;
    $sell_total = 0;
    $sell_table = '<h2>عينيات صادرة</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th width="5%" bgcolor="#eee">#</th>
              <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
              <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
              <th width="15%" bgcolor="#eee">المبلغ المدفوع</th>
              <th width="20%" bgcolor="#eee">المبلغ المتبقي</th>
              <th width="20%" bgcolor="#eee">الملاحظات</th>
            </tr>
          </thead>
          <tbody>';
    foreach ($customer_sell as $export_ainiat) {
      $remaining = '';
      if ($export_ainiat->remaining_balance > 0) {
        $remaining = $export_ainiat->remaining_balance . '<span>&#8362;&#160;</span> - دائن -';
      } else if ($export_ainiat->remaining_balance < 0) {
        $remaining = $export_ainiat->remaining_balance . '<span>&#8362;&#160;</span> - مدين -';
      } else {
        $remaining = $remaining = $export_ainiat->remaining_balance . '<span>&#8362;&#160;</span>';
      }
      $sell_table .= '<tr>
                <td width="5%">' . $i . '</td>
                <td width="20%">' . $export_ainiat->number . '</td>
                <td width="20%">' . $export_ainiat->date_created . '</td>
                <td width="15%">' . $export_ainiat->paid_balance . '</td>
                <td width="20%">' . $remaining . '</td>
                <td width="20%">' . $export_ainiat->notes . '</td>
              </tr>';
      $sell_total += $export_ainiat->remaining_balance;
      $i++;
    }
    if ($sell_total > 0) {
      $sell_total = $sell_total . '<span>&#8362;&#160;</span> - دائن -';
    } else if ($sell_total < 0) {
      $sell_total = $sell_total . '<span>&#8362;&#160;</span> - مدين -';
    } else {
      $sell_total = $sell_total . '<span>&#8362;&#160;</span>';
    }

    $sell_table .= '</tbody></table>';

    PDF::SetTitle('كشف حساب');
    PDF::SetAuthor('اياد الهسي');
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
    PDF::writeHTML($sarf_table);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $sarf_total . '<span>&#8362;&#160;</span> - مدين -</td></tr></tbody></table>');

    PDF::writeHTML($qapd_table);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $qapd_total . '<span>&#8362;&#160;</span> - دائن -</td></tr></tbody></table>');

    PDF::writeHTML($ainiat_table);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $buy_total . '</td></tr></tbody></table>');

    PDF::writeHTML($buy_table);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $buy_total . '</td></tr></tbody></table>');

    PDF::writeHTML($sell_table);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $sell_total . '</td></tr></tbody></table>');

    $balance = '';
    if ($customer_ainiat->balance > 0) {
      $balance = $customer_ainiat->balance . '<span>&#8362;&#160;</span> - دائن -';
    } else if ($customer_ainiat->balance < 0) {
      $balance = $customer_ainiat->balance . '<span>&#8362;&#160;</span> - مدين -';
    } else {
      $balance = $customer_ainiat->balance . '<span>&#8362;&#160;</span>';
    }

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">الرصيد</td><td width="20%">' . $balance . '</td></tr></tbody></table>');
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/المرشحون' . '/' . date('Y-m-d'));
    // $directoryPath = '/media/ahmed/Downloads';
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/' . 'كشف مرشح_' . $customer_ainiat->name . '_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
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
