<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use DB;
use PDF;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $page = config('app.page');
        if($request->ajax()) {
            $customers = Customer::select('id','name', 'balance', 'notes', 'status')->orderBy('id', 'DESC')->paginate($page);
            $table = view('admin.customer.table', compact('customers'))->render();
            return response()->json(['table' => $table]);
        } else {
            $customers = Customer::select('id','name', 'balance', 'notes', 'status')->orderBy('id', 'DESC')->paginate($page);
            $pages = ceil(Customer::count()/$page);
            return view('admin.customer.index', compact('customers', 'pages'));
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $customer = new Customer;
            $customer->name = $request->name;
            $customer->balance = 0;
            if($request->notes == null){
                $customer->notes = 'لا يوجد';
            } else {
                $customer->notes = $request->notes;
            }
            $customer->save();
            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error']);
        }
    }
    public function to_pdf(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $customers = DB::select('SELECT name, balance, notes, status FROM customers WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = \Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف كل الزبائن</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%">الرقم</th>
            <th width="30%">الاسم</th>
            <th width="20%">الرصيد</th>
            <th width="20%">ملاحظات</th>
            <th width="20%">الحالة</th>
          </tr>
        </thead>
        <tbody>';
        foreach($customers as $customer) {
            $status = ''; $balance = '';
            if ($customer->status == 1) {
                $status = 'موجود';
            } else {
                $status = 'غير موجود';
            }
            if($customer->balance > 0) {
                $balance = $customer->balance.'<span>&#8362;&#160;</span> - دائن -';
            } elseif($customer->balance < 0) {
                $balance = $customer->balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $balance = $customer->balance.'<span>&#8362;&#160;</span>';
            }
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="30%">'.$customer->name.'</td>
              <td width="20%">'.$balance.'</td>
              <td width="20%">'.$customer->notes.'</td>
              <td width="20%">'.$status.'</td>
            </tr>';
            $total += $customer->balance; $i++;
        }
        if($total < 0) {
            $total = $total.'<span>&#8362;&#160;</span> - مدين -';
        } elseif($total > 0) {
            $total = $total.'<span>&#8362;&#160;</span> - دائن -';
        } else {
            $total = $total;
        }
        
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل الزبائن');
        PDF::SetAuthor('اياد الهسي');
        // set some language dependent data:
        $lg = Array();
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$total.'</td></tr></tbody></table>');
        PDF::Output('all_customers_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
    public function kashf_to_pdf(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $id = $request->id;
        
        $customer = DB::select('SELECT name, balance FROM customers WHERE id = :id', ['id' => $id]);
        $customer_sarf = DB::select('SELECT sanadat_sarfs.date_created, sanadat_sarfs.number, sanadat_sarfs.balance, sanadat_sarfs.byan FROM customers, sanadat_sarfs WHERE customers.id = sanadat_sarfs.customer_id AND customers.id = :id AND sanadat_sarfs.date_created >= :from AND sanadat_sarfs.date_created <= :to ORDER BY sanadat_sarfs.id DESC', ['id' => $id, 'from' => $from, 'to' => $to]);

        $customer_qapd = DB::select('SELECT sanadat_qapds.date_created, sanadat_qapds.number, sanadat_qapds.balance, sanadat_qapds.byan FROM customers, sanadat_qapds WHERE customers.id = sanadat_qapds.customer_id AND customers.id = :id AND sanadat_qapds.date_created >= :from AND sanadat_qapds.date_created <= :to ORDER BY sanadat_qapds.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);
        
        $customer_buy = DB::select('SELECT buy_bills.date_created, buy_bills.number, buy_bills.paid_balance, buy_bills.byan, buy_bills.remaining_balance FROM customers, buy_bills WHERE customers.id = buy_bills.customer_id AND customers.id = :id AND buy_bills.date_created >= :from AND buy_bills.date_created <= :to ORDER BY buy_bills.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $customer_sell = DB::select('SELECT sell_bills.date_created, sell_bills.number, sell_bills.paid_balance, sell_bills.byan, sell_bills.remaining_balance FROM customers, sell_bills WHERE customers.id = sell_bills.customer_id AND customers.id = :id AND sell_bills.date_created >= :from AND sell_bills.date_created <= :to ORDER BY sell_bills.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $i = 1; $sarf_total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = \Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف حساب</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'&#160;&#160;&#160;&#160;الاسم: '.$customer[0]->name.' - زبون</p></br>';
        $sarf_table = '<h2>سندات الصرف</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%">#</th>
            <th width="25%">رقم السند</th>
            <th width="20%">تاريخ الانشاء</th>
            <th width="20%">الرصيد</th>
            <th width="25%">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($customer_sarf as $sanadat_sarf) {
            $sarf_table .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="25%">'.$sanadat_sarf->number.'</td>
              <td width="20%">'.$sanadat_sarf->date_created.'</td>
              <td width="20%">'.$sanadat_sarf->balance.'<span>&#8362;&#160;</span> - مدين -</td>
              <td width="25%">'.$sanadat_sarf->byan.'</td>
            </tr>';
            $sarf_total += $sanadat_sarf->balance; $i++;
        }
        
        $sarf_table .= '</tbody></table>';

        $i = 1; $qapd_total = 0;
        $qapd_table = '<h2>سندات القبض</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%">#</th>
            <th width="25%">رقم السند</th>
            <th width="20%">تاريخ الانشاء</th>
            <th width="20%">الرصيد</th>
            <th width="25%">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($customer_qapd as $sanadat_qapd) {
            $qapd_table .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="25%">'.$sanadat_qapd->number.'</td>
              <td width="20%">'.$sanadat_qapd->date_created.'</td>
              <td width="20%">'.$sanadat_qapd->balance.'<span>&#8362;&#160;</span> - دائن -</td>
              <td width="25%">'.$sanadat_qapd->byan.'</td>
            </tr>';
            $qapd_total += $sanadat_qapd->balance; $i++;
        }
        
        $qapd_table .= '</tbody></table>';

        $i = 1; $buy_total = 0;
        $buy_table = '<h2>فواتير الشراء</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%">#</th>
            <th width="20%">رقم الفاتورة</th>
            <th width="20%">تاريخ الانشاء</th>
            <th width="15%">المبلغ المدفوع</th>
            <th width="20%">المبلغ المتبقي</th>
            <th width="20%">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($customer_buy as $buy_bill) {
            $remaining = '';
            if($buy_bill->remaining_balance > 0) {
                $remaining = $buy_bill->remaining_balance.'<span>&#8362;&#160;</span> - دائن -';
            } else if ($buy_bill->remaining_balance < 0) {
                $remaining = $buy_bill->remaining_balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $remaining = $remaining = $buy_bill->remaining_balance.'<span>&#8362;&#160;</span>';
            }
            $buy_table .= '<tr>
              <td width="5%">'.$i.'</td>
              <td width="20%">'.$buy_bill->number.'</td>
              <td width="20%">'.$buy_bill->date_created.'</td>
              <td width="15%">'.$buy_bill->paid_balance.'<span>&#8362;&#160;</span></td>
              <td width="20%">'.$remaining.'</td>
              <td width="20%">'.$buy_bill->byan.'</td>
            </tr>';
            $buy_total += $buy_bill->remaining_balance; $i++;
        }
        if ($buy_total > 0) {
            $buy_total = $buy_total.'<span>&#8362;&#160;</span> - دائن -';
        } else if ($buy_total < 0) {
            $buy_total = $buy_total.'<span>&#8362;&#160;</span> - مدين -';
        } else {
            $buy_total = $buy_total.'<span>&#8362;&#160;</span>';
        }
        
        $buy_table .= '</tbody></table>';

        $i = 1; $sell_total = 0;
        $sell_table = '<h2>فواتير البيع</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%">#</th>
            <th width="20%">رقم الفاتورة</th>
            <th width="20%">تاريخ الانشاء</th>
            <th width="15%">المبلغ المدفوع</th>
            <th width="20%">المبلغ المتبقي</th>
            <th width="20%">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($customer_sell as $sell_bill) {
            $remaining = '';
            if($sell_bill->remaining_balance > 0) {
                $remaining = $sell_bill->remaining_balance.'<span>&#8362;&#160;</span> - دائن -';
            } else if ($sell_bill->remaining_balance < 0) {
                $remaining = $sell_bill->remaining_balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $remaining = $remaining = $sell_bill->remaining_balance.'<span>&#8362;&#160;</span>';
            }
            $sell_table .= '<tr>
              <td width="5%">'.$i.'</td>
              <td width="20%">'.$sell_bill->number.'</td>
              <td width="20%">'.$sell_bill->date_created.'</td>
              <td width="15%">'.$sell_bill->paid_balance.'<span>&#8362;&#160;</span></td>
              <td width="20%">'.$remaining.'</td>
              <td width="20%">'.$sell_bill->byan.'</td>
            </tr>';
            $sell_total += $sell_bill->remaining_balance; $i++;
        }
        if ($sell_total > 0) {
            $sell_total = $sell_total.'<span>&#8362;&#160;</span> - دائن -';
        } else if ($sell_total < 0) {
            $sell_total = $sell_total.'<span>&#8362;&#160;</span> - مدين -';
        } else {
            $sell_total = $sell_total.'<span>&#8362;&#160;</span>';
        }
        
        $sell_table .= '</tbody></table>';

        PDF::SetTitle('كشف حساب');
        PDF::SetAuthor('اياد الهسي');
        // set some language dependent data:
        $lg = Array();
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
        PDF::writeHTML($sarf_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$sarf_total.'<span>&#8362;&#160;</span> - مدين -</td></tr></tbody></table>');

        PDF::writeHTML($qapd_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$qapd_total.'<span>&#8362;&#160;</span> - دائن -</td></tr></tbody></table>');
        
        PDF::writeHTML($buy_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$buy_total.'</td></tr></tbody></table>');

        PDF::writeHTML($sell_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$sell_total.'</td></tr></tbody></table>');
        
        $balance = '';
        if ($customer[0]->balance > 0) {
            $balance = $customer[0]->balance.'<span>&#8362;&#160;</span> - دائن -';
        } else if ($customer[0]->balance < 0) {
            $balance = $customer[0]->balance.'<span>&#8362;&#160;</span> - مدين -';
        } else {
            $balance = $customer[0]->balance.'<span>&#8362;&#160;</span>';
        }

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">الرصيد</td><td width="20%">'.$balance.'</td></tr></tbody></table>');

        PDF::Output('provider_kashf_hisab_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
