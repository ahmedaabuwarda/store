<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Worker;
use DB;
use PDF;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function create(Request $request)
    {
        $id = $request->id;
        $worker = DB::select('SELECT id, name, balance FROM workers WHERE id = :id', ['id' => $id]);
        if ($worker != null) {
            return response()->json(['status' => 'success', 'worker' => $worker]);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $balance = abs($request->balance);
            $worker = new Salary;
            $worker->worker_id = $request['worker_id'];
            $worker->remaining_balance = $request['net_balance'] - $balance;
            $worker->balance = $request['balance'];
            $worker->net_balance = $request['net_balance'];
            $worker->date_created = $request['date_created'];
            if($request['notes'] == null){
                $worker->notes = 'لا يوجد';
            } else {
                $worker->notes = $request['notes'];
            }
            $worker->save();
            if($request['net_balance'] >= 0) {
                Worker::where('id', $request['worker_id'])->update(['balance' => 0]);
            } else {
                Worker::where('id', $request['worker_id'])->update(['balance' => $request['net_balance']]);
            }

            DB::statement('UPDATE box SET box.remaining = CASE box.id 
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
                WHEN 8 THEN (SELECT remaining FROM box WHERE box.id = 8)+?
                ELSE box.remaining
                END,
            box.counter = CASE box.id 
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                WHEN 8 THEN (SELECT counter FROM box WHERE box.id = 8)+1 
                ELSE box.counter
                END
            WHERE box.id IN(1, 8);', [$balance, $balance]);

            $date = date($request['date_created'].' H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,0,?,?)',[$balance,'راتب', $date]);
            
            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error']);
        }
    }
    public function to_pdf(Request $request)
    {
        $from = $request['from'];
        $to = $request['to'];

        $salaries = DB::select('SELECT salaries.remaining_balance, salaries.balance, salaries.net_balance, salaries.date_created, salaries.notes, workers.name, workers.id FROM salaries, workers WHERE salaries.worker_id = workers.id AND salaries.date_created >= :from AND salaries.date_created <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = \Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف كل رواتب الموظفين</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%">الرقم</th>
            <th width="20%">تاريخ الانشاء</th>
            <th width="20%">المستهلك</th>
            <th width="10%">رصيد متبقي</th>
            <th width="10%">راتب اساسي</th>
            <th width="10%">صافي الراتب</th>
            <th width="20%">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
        foreach($salaries as $salary) {
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="20%">'.$salary->date_created.'</td>
              <td width="20%">'.$salary->name.'</td>
              <td width="10%">'.$salary->remaining_balance.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$salary->balance.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$salary->net_balance.'<span>&#8362;&#160;</span></td>
              <td width="20%">'.$salary->notes.'</td>
            </tr>';
            $total += $salary->net_balance; $i++;
        }
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل الرواتب');
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$total.'<span>&#8362;&#160;</span></td></tr></tbody></table>');
        PDF::Output('all_workers_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
