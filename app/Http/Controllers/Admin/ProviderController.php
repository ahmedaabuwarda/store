<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Provider;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = config('app.page');
        if($request->ajax()){
            $providers = Provider::select('id', 'name', 'balance', 'notes', 'status')->orderBy('id', 'DESC')->paginate($page);
            $table = view('admin.provider.table', compact('providers'))->render();
            return response()->json(['table' => $table]);
        } else {
            $providers = Provider::select('id', 'name', 'balance', 'notes', 'status')->orderBy('id', 'DESC')->paginate($page);
            $pages = ceil(Provider::count()/$page);
            return view('admin.provider.index', compact('providers', 'pages'));
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $provider = new Provider;
            $provider->name = $request->name;
            $provider->balance = 0;
            if($request->notes == null){
                $provider->notes = 'لا يوجد';
            } else {
                $provider->notes = $request->notes;
            }
            $provider->save();

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
        $providers = DB::select('SELECT name, balance, notes, status FROM providers WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف كل الموردين</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="30%" bgcolor="#eee">الاسم</th>
            <th width="20%" bgcolor="#eee">الرصيد</th>
            <th width="20%" bgcolor="#eee">ملاحظات</th>
            <th width="20%" bgcolor="#eee">الحالة</th>
          </tr>
        </thead>
        <tbody>';
        foreach($providers as $provider) {
            $status = ''; $balance = '';
            if ($provider->status == 1) {
                $status = 'موجود';
            } else {
                $status = 'غير موجود';
            }
            if($provider->balance > 0){
                $balance = $provider->balance.'<span>&#8362;&#160;</span> - دائن -';
            } elseif($provider->balance < 0) {
                $balance = $provider->balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $balance = $provider->balance.'<span>&#8362;&#160;</span>';
            }
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="30%">'.$provider->name.'</td>
              <td width="20%">'.$balance.'</td>
              <td width="20%">'.$provider->notes.'</td>
              <td width="20%">'.$status.'</td>
            </tr>';
            $total += $provider->balance; $i++;
        }
        if($total < 0){
            $total = $total.'<span>&#8362;&#160;</span> - مدين -';
        } elseif($total > 0){
            $total = $total.'<span>&#8362;&#160;</span> - دائن -';
        } else {
            $total = $total;
        }

        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل الموردين');
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
        PDF::Output('all_providers_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }

    public function kashf_to_pdf(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $id = $request->id;

        $provider = DB::select('SELECT name, balance FROM providers WHERE id = :id', ['id' => $id]);
        $provider_sarf = DB::select('SELECT sanadat_sarfs.date_created, sanadat_sarfs.number, sanadat_sarfs.balance, sanadat_sarfs.byan FROM providers, sanadat_sarfs WHERE providers.id = sanadat_sarfs.provider_id AND providers.id = :id AND sanadat_sarfs.date_created >= :from AND sanadat_sarfs.date_created <= :to ORDER BY sanadat_sarfs.id DESC', ['id' => $id, 'from' => $from, 'to' => $to]);

        $provider_qapd = DB::select('SELECT sanadat_qapds.date_created, sanadat_qapds.number, sanadat_qapds.balance, sanadat_qapds.byan FROM providers, sanadat_qapds WHERE providers.id = sanadat_qapds.provider_id AND providers.id = :id AND sanadat_qapds.date_created >= :from AND sanadat_qapds.date_created <= :to ORDER BY sanadat_qapds.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $provider_buy = DB::select('SELECT buy_bills.date_created, buy_bills.number, buy_bills.paid_balance, buy_bills.byan, buy_bills.remaining_balance, buy_bills.discount FROM providers, buy_bills WHERE providers.id = buy_bills.provider_id AND providers.id = :id AND buy_bills.date_created >= :from AND buy_bills.date_created <= :to ORDER BY buy_bills.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $provider_sell = DB::select('SELECT sell_bills.date_created, sell_bills.number, sell_bills.paid_balance, sell_bills.byan, sell_bills.remaining_balance FROM providers, sell_bills WHERE providers.id = sell_bills.provider_id AND providers.id = :id AND sell_bills.date_created >= :from AND sell_bills.date_created <= :to ORDER BY sell_bills.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $i = 1; $sarf_total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف حساب</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'&#160;&#160;&#160;&#160;الاسم: '.$provider[0]->name.' - مورد</p></br>';
        $sarf_table = '<h2>سندات الصرف</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">#</th>
            <th width="25%" bgcolor="#eee">رقم السند</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">الرصيد</th>
            <th width="25%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($provider_sarf as $sanadat_sarf) {
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
            <th width="10%" bgcolor="#eee">#</th>
            <th width="25%" bgcolor="#eee">رقم السند</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">الرصيد</th>
            <th width="25%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($provider_qapd as $sanadat_qapd) {
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

        $i = 1; $buy_total = 0; $discount_total = 0;
        $buy_table = '<h2>فواتير الشراء</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">#</th>
            <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="15%" bgcolor="#eee">المبلغ المدفوع</th>
            <th width="10%" bgcolor="#eee">المبلغ المتبقي</th>
            <th width="10%" bgcolor="#eee">الخصم</th>
            <th width="20%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($provider_buy as $buy_bill) {
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
              <td width="10%">'.$remaining.'</td>
              <td width="10%">'.$buy_bill->discount.'<span>&#8362;&#160;</span></td>
              <td width="20%">'.$buy_bill->byan.'</td>
            </tr>';
            $buy_total += $buy_bill->remaining_balance; $discount_total += $buy_bill->discount; $i++;
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
            <th width="5%" bgcolor="#eee">#</th>
            <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="15%" bgcolor="#eee">المبلغ المدفوع</th>
            <th width="20%" bgcolor="#eee">المبلغ المتبقي</th>
            <th width="20%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($provider_sell as $sell_bill) {
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
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center">
            <tbody>
                <tr>
                    <td width="10%">#</td>
                    <td width="30%">المجموع</td>
                    <td width="20%">'.$sarf_total.'<span>&#8362;&#160;</span> - مدين -</td>
                </tr>
            </tbody>
        </table>');

        PDF::writeHTML($qapd_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$qapd_total.'<span>&#8362;&#160;</span> - دائن -</td></tr></tbody></table>');

        PDF::writeHTML($buy_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center">
            <tbody>
                <tr>
                    <td width="10%">#</td>
                    <td width="30%">المجموع</td>
                    <td width="20%">'.$buy_total.'</td>
                </tr>
                <tr>
                    <td width="10%">#</td>
                    <td width="30%">الخصم</td>
                    <td width="20%">'.$discount_total.'<span>&#8362;&#160;</span></td>
                </tr>
            </tbody>
        </table>');

        PDF::writeHTML($sell_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$sell_total.'</td></tr></tbody></table>');

        $balance = '';
        if ($provider[0]->balance > 0) {
            $balance = $provider[0]->balance.'<span>&#8362;&#160;</span> - دائن -';
        } else if ($provider[0]->balance < 0) {
            $balance = $provider[0]->balance.'<span>&#8362;&#160;</span> - مدين -';
        } else {
            $balance = $provider[0]->balance.'<span>&#8362;&#160;</span>';
        }

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">الرصيد</td><td width="20%">'.$balance.'</td></tr></tbody></table>');

        PDF::Output('provider_kashf_hisab_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }

}