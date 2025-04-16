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

use App\Models\Mosque;
use App\Models\Product;
use App\Models\Sanadat_Sarf;
use App\Models\Sanadat_Qapd;
use App\Models\Selective;

use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
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
    $customers = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'notes', 'status', 'mosque_id', 'created_at')->with('mosque:id,name')->orderBy('id', 'DESC')->paginate($page);
    $mosques = Mosque::select('id', 'name')->get();

    if ($request->ajax()) {
      $table = view('admin.customer.table', compact('customers'))->render();
      return response()->json(['table' => $table]);
    } else {
      $pages = ceil(Customer::count() / $page);
      return view('admin.customer.index', compact('customers', 'pages', 'mosques'));
    }
  }

  // store
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
            // check if customer exists then update it if not then add new one
            $customer = Customer::where('identity', $row[1])->first();
            // dd($customer);
            if ($customer == null) {
              $customer = new Customer();
            }
            $customer->identity = $row[1];
            $customer->name = $row[2];
            $customer->phone = $row[3];
            $customer->family_number = $row[4];
            $customer->balance = 0;
            $mosque = Mosque::where('name', $row[5])->first();
            if ($mosque) {
              $customer->mosque_id = $mosque->id;
            }
            $customer->notes = $row[6] ?? 'لا يوجد';
            $customer->save();
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة الستفيدون بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        // return dd($e->getMessage());  // For debugging
        // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ المستفيدون']);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }
    } else {

      $mosque_id = $request->mosque_id;

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
        $customer->mosque_id = $mosque_id;
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
    $customer = Customer::where('id', $id)->with('mosque:id,name')->first();
    $mosques = Mosque::select('id', 'name')->get();
    $products = Product::select('id', 'name')->get();
    return view('admin.customer.edit', compact('customer', 'mosques', 'products'));
  }

  // update
  public function update(Request $request)
  {

    $user_id = Auth::user()->id;
    $customer_id = $request->id;
    $name = $request->name;
    $phone = $request->phone;
    $identity = $request->identity;
    $family_number = $request->family_number;
    $mosque_id = $request->mosque_id;
    $notes = $request->notes;
    $status = $request->status;
    $product_id = $request->product_id;

    DB::beginTransaction();
    try {

      if ($status == 0) {
        $selective = Selective::where('product_id', $product_id)
        ->where('status', 0)
        ->where('customer_id', $customer_id)
        ->get();
        if ($selective->count() == 0) {
          $selective = new Selective();
          $selective->user_id = $user_id;
          $selective->customer_id = $customer_id;
          $selective->product_id = $product_id;
          $selective->status = 0;
          $selective->save();
        }
      }

      Customer::where('id', $customer_id)->update([
        'name' => $name,
        'phone' => $phone,
        'identity' => $identity,
        'family_number' => $family_number,
        'mosque_id' => $mosque_id,
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

  // to pdf
  public function to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');

    $customers = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'notes', 'status', 'mosque_id', 'created_at')
      ->with('mosque:id,name')
      ->whereBetween('created_at', [$from, $to])
      ->orderBy('id', 'DESC')
      ->get();

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
    PDF::writeHTML($table_content);
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/المستفيدون' . '/' . date('Y-m-d'));
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

  // kashf hesap
  public function kashf_to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');
    $id = $request->id;

    // $customer = DB::select('SELECT name, balance, phone, identity, family_number FROM customers WHERE id = :id', ['id' => $id]);

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
    </br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br><p>الاسم: ' . $customer_ainiat->name . ' - مستفيد' . '&#160;&#160;رقم الهوية: ' . $customer_ainiat->identity . '&#160;&#160;رقم الجوال: ' . $customer_ainiat->phone . '&#160;&#160;عدد افراد الاسرة: ' . $customer_ainiat->family_number . '</p></br>';

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
      $status = $selective->status == 0 ? 'مرشج' : 'مستفيد';
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
    $directoryPath = storage_path('app/public/pdf/المستفيدون' . '/' . date('Y-m-d'));
    // $directoryPath = '/media/ahmed/Downloads';
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/' . 'كشف مستفيد_' . $customer_ainiat->name . '_' . date('Y-m-d-his') . '.pdf';
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
    $fileName = 'كشف المستفيدون_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/المستفيدون' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new CustomerExport(), 'xlsx/المستفيدون' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
