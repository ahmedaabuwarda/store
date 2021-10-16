<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Models\User;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\Sanadat_Qapd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SanadatQapdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $page = config('app.page');
        $sanadat_qapds = Sanadat_Qapd::select('id', 'number', 'date_created', 'balance', 'byan','provider_id', 'customer_id', 'worker_id')->with('user:id,name')->with('customer:id,name')->with('provider:id,name')->orderBy('date_created', 'DESC')->paginate($page);
        $box = DB::select('SELECT remaining from box where id = 4');

        // if the request is ajax
        if($request->ajax()){

            $table = view('admin.sanadat_qapd.table', compact('sanadat_qapds'))->render();
            return response()->json(['table' => $table]);

        // if the request is not ajax
        } else {

            $customers = DB::select('SELECT id, name FROM customers ORDER BY id DESC');
            $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
            $workers = DB::select('SELECT id, name FROM users ORDER BY id DESC');
            $pages = ceil(Sanadat_Qapd::count()/$page);
            return view('admin.sanadat_qapd.index', compact('sanadat_qapds', 'customers', 'providers', 'workers', 'pages', 'box'));

        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $balance = abs($request['balance']);
            $customer_id = $request['customer_id'];
            $provider_id = $request['provider_id'];
            $worker_id = $request['worker_id'];
            $target = '';

            $sanadat_qapd = new Sanadat_Qapd;
            $sanadat_qapd->number = $request['number'];
            $sanadat_qapd->date_created = $request['date_created'];
            $sanadat_qapd->balance = $balance;
            if($request['byan'] == null){
                $sanadat_qapd->byan = 'لا يوجد';
            } else {
                $sanadat_qapd->byan = $request['byan'];
            }

            if ($request['target'] == 'customers') {
                $customer = Customer::where('id', $customer_id)->select('name', 'balance')->first();
                if($customer != null){
                    Customer::where('id', $customer_id)->update(['balance' => $customer->balance + $balance]);
                    $sanadat_qapd->customer_id = $customer_id;
                    $target = $customer->name;
                } else {
                    DB::rollback();
                    return response()->json(['status' => 'error']);
                }
            } elseif ($request['target'] == 'providers') {
                $provider = Provider::where('id', $provider_id)->select('name', 'balance')->first();
                if($provider != null){
                    Provider::where('id', $provider_id)->update(['balance' => $provider->balance + $balance]);
                    $sanadat_qapd->provider_id = $provider_id;
                    $target = $provider->name;
                } else {
                    DB::rollback();
                    return response()->json(['status' => 'error']);
                }
            } elseif ($request['target'] == 'workers') {
                $worker = User::where('id', $worker_id)->select('name', 'balance')->first();
                if($worker != null){
                    User::where('id', $worker_id)->update(['balance' => $worker->balance + $balance]);
                    $sanadat_qapd->worker_id = $worker_id;
                    $target = $worker->name;
                } else {
                    DB::rollback();
                    return response()->json(['status' => 'error']);
                }
            }
            $sanadat_qapd->save();

            DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)+?
                WHEN 4 THEN (SELECT remaining FROM box WHERE box.id = 4)+?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                WHEN 4 THEN (SELECT counter FROM box WHERE box.id = 4)+1
                ELSE box.counter
                END
            WHERE box.id IN(1, 4);', [$balance, $balance]);

            $date = date($request['date_created'].' H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,1,?,?)',[$balance, 'سند قبض', $date]);

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error' . $e->getMessage()]);
        }
    }
    public function delete(Request $request)
    {
        DB::beginTransaction();
        $id = $request['id'];
        $sadat_qapd = Sanadat_Qapd::where('id', $id)->first();
        try {
            $customer_id = $sadat_qapd->customer_id;
            $provider_id = $sadat_qapd->provider_id;
            $worker_id = $sadat_qapd->worker_id;
            $balance = $sadat_qapd->balance;

            if ($sadat_qapd != null && $provider_id > 0) {
                $provider = Provider::where('id', $provider_id)->select('balance')->first();
                if($provider != null){
                    Provider::where('id', $provider_id)->update(['balance' => $provider->balance - $balance]);
                } else {
                    DB::rollback();
                    return response()->json(['status' => 'error']);
                }
            } elseif ($sadat_qapd != null && $customer_id > 0) {
                $customer = Customer::where('id', $customer_id)->select('balance')->first();
                if($customer != null){
                    Customer::where('id', $customer_id)->update(['balance' => $customer->balance - $balance]);
                } else {
                    DB::rollback();
                    return response()->json(['status' => 'error']);
                }
            } elseif ($sadat_qapd != null && $worker_id > 0) {
                $worker = User::where('id', $worker_id)->select('balance')->first();
                if($worker != null){
                    User::where('id', $worker_id)->update(['balance' => $worker->balance - $balance]);
                } else {
                    DB::rollback();
                    return response()->json(['status' => 'error']);
                }
            } else {
                return response()->json(['status' => 'error']);
            }

            DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
                WHEN 4 THEN (SELECT remaining FROM box WHERE box.id = 4)-?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                WHEN 4 THEN (SELECT counter FROM box WHERE box.id = 4)-1
                ELSE box.counter
                END
            WHERE box.id IN(1, 4);', [$balance, $balance]);

            $date = date('Y-m-d H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,0,?,?)',[$balance, 'سند قبض', $date]);

            $sadat_qapd->delete();
            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error']);
        }
    }
    public function to_pdf(Request $request)
    {
        $from = $request['from'];
        $to = $request['to'];
        $sanadat_qapds = Sanadat_Qapd::select('id', 'number', 'date_created', 'balance', 'byan','provider_id', 'customer_id', 'worker_id')->with('user:id,name')->with('customer:id,name')->with('provider:id,name')->whereRaw('date_created >= ? AND date_created <= ?',[$from, $to])->orderBy('id', 'DESC')->get();

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">محلات النور - ابووردة لقطع غيار الدراجات النارية</h3><h1 align="center">كشف كل سندات القبض</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="20%" bgcolor="#eee">رقم السند</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">المستهلك</th>
            <th width="10%" bgcolor="#eee">الرصيد</th>
            <th width="20%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($sanadat_qapds as $sanadat_qapd) {
            $target = '';
            if($sanadat_qapd->provider_id > 0){
                $target = $sanadat_qapd->provider->name.' - مورد';
            } elseif($sanadat_qapd->customer_id > 0){
                $target = $sanadat_qapd->customer->name.' - زبون';
            } elseif($sanadat_qapd->worker_id > 0){
                $target = $sanadat_qapd->worker->name.' - موظف';
            }
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="20%">'.$sanadat_qapd->number.'</td>
              <td width="20%">'.$sanadat_qapd->date_created.'</td>
              <td width="20%">'.$target.'</td>
              <td width="10%">'.$sanadat_qapd->balance.'<span>&#8362;&#160;</span></td>
              <td width="20%">'.$sanadat_qapd->byan.'</td>
            </tr>';
            $total += $sanadat_qapd->balance; $i++;
        }
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل سندات القبض');
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%" color="#fff" bgcolor="#003B36">'.$total.'<span>&#8362;&#160;</span></td></tr></tbody></table>');
        PDF::Output('all_sanadat_qapd_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
