<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Imports\CustomerImport;
use App\Exports\CustomerExport;
use App\Models\Sanadat_Sarf;
use App\Models\Sanadat_Qapd;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $page = config('app.page');
    $customers = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'notes', 'status', 'created_at')->orderBy('id', 'DESC')->paginate($page);

    if ($request->ajax()) {
      $table = view('admin.customer.table', compact('customers'))->render();
      return response()->json(['table' => $table]);
    } else {
      $pages = ceil(Customer::count() / $page);
      return view('admin.customer.index', compact('customers', 'pages'));
    }
  }

  public function store(Request $request)
  {
    $file_attachment = $request->file('file_attachment');

    $xlsx_data = null;

    if ($file_attachment != null && $request->name == null) {
      // Read the xlsx file
      $xlsx_data = Excel::toArray(new CustomerImport, $file_attachment);

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

            $customer = new Customer;
            $customer->name = $row[1];
            $customer->identity = $row[2];
            $customer->phone = $row[3];
            $customer->family_number = $row[4];
            $customer->balance = 0;
            $customer->notes = $row[5] ?? 'لا يوجد';
            $customer->save();
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة الستفيدون بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        return dd($e->getMessage());  // For debugging
        return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ المستفيدون']);
      }
    } else {
      if ($request->name == null || $request->identity == null || $request->phone == null || $request->family_number == null) {
        return response()->json(['status' => 'error', 'message' => 'بعض الحقول مطلوبة!']);
      }
      DB::beginTransaction();
      try {
        $customer = new Customer;
        $customer->name = $request->name;
        $customer->identity = $request->identity;
        $customer->phone = $request->phone;
        $customer->family_number = $request->family_number;
        $customer->balance = 0;
        $customer->notes = $request->notes ?? 'لا يوجد';
        $customer->save();

        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة المستفيد بنجاح']);
      } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['status' => 'error', 'message' => 'حدث خطا اثناء اضافة المستفيد!']);
      }
    }
  }

  // edit
  public function edit(Request $request, $id)
  {
    $customer = Customer::where('id', $id)->first();
    return view('admin.customer.edit', compact('customer'));
  }

  // update
  public function update(Request $request)
  {
    $customer_id = $request->id;
    $name = $request->name;
    $phone = $request->phone;
    $identity = $request->identity;
    $family_number = $request->family_number;
    $notes = $request->notes;
    $status = $request->status;

    DB::beginTransaction();
    try {

      Customer::where('id', $customer_id)->update([
        'name' => $name,
        'phone' => $phone,
        'identity' => $identity,
        'family_number' => $family_number,
        'notes' => $notes,
        'status' => $status,
      ]);

      DB::commit();
      return redirect('/customers')->with('success', 'تم تحديث المستفيد بنجاح');
    } catch (\Exception $e) {
      DB::rollback();
      return redirect('/customers')->with('error', 'حدث خطا اثناء تحديث المستفيد!');
    }
  }

  public function to_pdf(Request $request)
  {
    $from = $request->from;
    $to = $request->to;
    $customers = DB::select('SELECT name, balance, notes, identity ,family_number, phone, status FROM customers WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);
    $i = 1;
    $total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل المستفيدون</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">الرقم</th>
            <th width="25%" bgcolor="#eee">الاسم</th>
            <th width="20%" bgcolor="#eee">رقم الهوية</th>
            <th width="20%" bgcolor="#eee">رقم الجوال</th>
            <th width="5%" bgcolor="#eee"عدد افراد الاسرة</th>
            <th width="25%" bgcolor="#eee">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($customers as $customer) {
      $status = '';
      $balance = '';
      if ($customer->status == 1) {
        $status = 'موجود';
      } else {
        $status = 'غير موجود';
      }
      if ($customer->balance > 0) {
        $balance = $customer->balance . '<span>&#8362;&#160;</span> - دائن -';
      } elseif ($customer->balance < 0) {
        $balance = $customer->balance . '<span>&#8362;&#160;</span> - مدين -';
      } else {
        $balance = $customer->balance . '<span>&#8362;&#160;</span>';
      }
      $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="25%">' . $customer->name . '</td>
              <td width="20%">' . $customer->identity . '</td>
              <td width="20%">' . $customer->phone . '</td>
              <td width="5%">' . $customer->family_number . '</td>
              <td width="25%">' . $customer->notes . '</td>
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
    PDF::SetTitle('كل المستفيدون');
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
    // dd($table_content);
    PDF::writeHTML($table_content);
    // dd($table_content);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $total . '</td></tr></tbody></table>');
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/المستفيدون');
    // $directoryPath = '/media/ahmed/Downloads';
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف المستفيدون_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('all_customers_' . date('ymdhis') . '.pdf', 'I');
    return response()->json(['status' => 'success']);
  }

  public function kashf_to_pdf(Request $request)
  {
    $from = $request->from;
    $to = $request->to;
    $id = $request->id;

    $customer = DB::select('SELECT name, balance, phone, identity, family_number FROM customers WHERE id = :id', ['id' => $id]);

    $customer_sarf = Sanadat_Sarf::select('id', 'date_created', 'number', 'balance', 'byan', 'customer_id', 'box_id', 'user_id')
      ->with(['customer', 'box', 'user'])
      ->whereBetween('date_created', [$from, $to])
      ->where('customer_id', $id)
      ->orderBy('id', 'DESC')
      ->get();

    $customer_qapd = Sanadat_Qapd::select('id', 'date_created', 'number', 'balance', 'byan', 'customer_id', 'box_id', 'user_id')
      ->with(['customer', 'box', 'user'])
      ->whereBetween('date_created', [$from, $to])
      ->where('customer_id', $id)
      ->orderBy('id', 'DESC')
      ->get();

    $customer_buy = DB::select('SELECT buy_bills.date_created, buy_bills.number, buy_bills.paid_balance, buy_bills.byan, buy_bills.remaining_balance FROM customers, buy_bills WHERE customers.id = buy_bills.customer_id AND customers.id = :id AND buy_bills.date_created >= :from AND buy_bills.date_created <= :to ORDER BY buy_bills.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

    $customer_sell = DB::select('SELECT export_ainiats.date_created, export_ainiats.number, export_ainiats.paid_balance, export_ainiats.byan, export_ainiats.remaining_balance FROM customers, export_ainiats WHERE customers.id = export_ainiats.customer_id AND customers.id = :id AND export_ainiats.date_created >= :from AND export_ainiats.date_created <= :to ORDER BY export_ainiats.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

    $i = 1;
    $sarf_total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;
    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4>
    <h1 align="center">كشف حساب</h1>
    </br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br><p>الاسم: ' . $customer[0]->name . ' - مستفيد' . '&#160;&#160;رقم الهوية: ' . $customer[0]->identity . '&#160;&#160;رقم الجوال: ' . $customer[0]->phone . '&#160;&#160;عدد افراد الاسرة: ' . $customer[0]->family_number . '</p></br>';
    $sarf_table = '<h2>سندات الصرف</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">#</th>
            <th width="15%" bgcolor="#eee">رقم السند</th>
            <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="10%" bgcolor="#eee">المبلغ</th>
            <th width="20%" bgcolor="#eee">لصنندوق</th>
            <th width="15%" bgcolor="#eee">بواسطة</th>
            <th width="20%" bgcolor="#eee">البيان</th>
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
              <td width="20%">' . $sanadat_sarf->byan . '</td>
            </tr>';
      $sarf_total += $sanadat_sarf->balance;
      $i++;
    }

    $sarf_table .= '</tbody></table>';

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
            <th width="20%" bgcolor="#eee">البيان</th>
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
              <td width="20%">' . $sanadat_qapd->byan . '</td>
            </tr>';
      $qapd_total += $sanadat_qapd->balance;
      $i++;
    }

    $qapd_table .= '</tbody></table>';

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
            <th width="20%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($customer_buy as $buy_bill) {
      $remaining = '';
      if ($buy_bill->remaining_balance > 0) {
        $remaining = $buy_bill->remaining_balance . '<span>&#8362;&#160;</span> - دائن -';
      } else if ($buy_bill->remaining_balance < 0) {
        $remaining = $buy_bill->remaining_balance . '<span>&#8362;&#160;</span> - مدين -';
      } else {
        $remaining = $remaining = $buy_bill->remaining_balance . '<span>&#8362;&#160;</span>';
      }
      $buy_table .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="20%">' . $buy_bill->number . '</td>
              <td width="20%">' . $buy_bill->date_created . '</td>
              <td width="15%">' . $buy_bill->paid_balance . '<span>&#8362;&#160;</span></td>
              <td width="20%">' . $remaining . '</td>
              <td width="20%">' . $buy_bill->byan . '</td>
            </tr>';
      $buy_total += $buy_bill->remaining_balance;
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
            <th width="20%" bgcolor="#eee">البيان</th>
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
              <td width="20%">' . $export_ainiat->byan . '</td>
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

    PDF::writeHTML($buy_table);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $buy_total . '</td></tr></tbody></table>');

    PDF::writeHTML($sell_table);
    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $sell_total . '</td></tr></tbody></table>');

    $balance = '';
    if ($customer[0]->balance > 0) {
      $balance = $customer[0]->balance . '<span>&#8362;&#160;</span> - دائن -';
    } else if ($customer[0]->balance < 0) {
      $balance = $customer[0]->balance . '<span>&#8362;&#160;</span> - مدين -';
    } else {
      $balance = $customer[0]->balance . '<span>&#8362;&#160;</span>';
    }

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">الرصيد</td><td width="20%">' . $balance . '</td></tr></tbody></table>');
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/المستفيدون');
    // $directoryPath = '/media/ahmed/Downloads';
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/' . 'كشف مستفيد_' . $customer[0]->name . '_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('provider_kashf_hisab_' . date('ymdhis') . '.pdf', 'D');
    return response()->json(['status' => 'success']);

  }
  public function to_xlsx(Request $request)
  {
    // dd(date($request->from . ' H:i:s'));
    $fileName = 'كشف المستفيدون_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/المستفيدون');
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new CustomerExport(), 'xlsx/المستفيدون/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
