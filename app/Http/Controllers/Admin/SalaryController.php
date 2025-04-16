<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Salary;
use App\Models\Worker;
use App\Models\Box;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    $user_id = Auth::user()->id;
    $box_id = $request->box_id;
    $worker_id = $request['worker_id'];
    $net_balance = $request['net_balance'];
    $balance = $request['balance'];
    $date_created = $request['date_created'];
    $notes = $request['notes'];

    DB::beginTransaction();
    try {
      $balance = abs($request->balance);
      $net_balance = abs($request->net_balance);
      $worker = new Salary;
      $worker->worker_id = $worker_id;
      $worker->remaining_balance = $net_balance - $balance;
      $worker->balance = $balance;
      $worker->net_balance = $net_balance;
      $worker->user_id = $user_id;
      $worker->box_id = $box_id;
      $worker->date_created = $date_created;
      $worker->notes = $notes ?? 'لا يوجد';
      $worker->save();
      if ($net_balance >= 0) {
        Worker::where('id', $worker_id)->update(['balance' => 0]);
      } else {
        Worker::where('id', $worker_id)->update(['balance' => $net_balance]);
      }

      $box = Box::select('id', 'balance', 'counter')->where('id', $box_id)->first();

      $box->update([
        'balance' => $box->balance - $balance,
        'counter' => $box->counter + 1,
      ]);

      $date = date($request['date_created'] . ' H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,0,?,?,?,?)', [$balance, 'راتب', $date, $box_id, $user_id]);

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['status' => 'error']);
    }
  }

  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');

    $salaries = DB::select('SELECT salaries.remaining_balance, salaries.balance, salaries.net_balance, salaries.date_created, salaries.notes, workers.name, workers.id FROM salaries, workers WHERE salaries.worker_id = workers.id AND salaries.date_created >= :from AND salaries.date_created <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

    $i = 1;
    $total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;


    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف كل رواتب الموظفين</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">المستهلك</th>
            <th width="10%" bgcolor="#eee">رصيد متبقي</th>
            <th width="10%" bgcolor="#eee">راتب اساسي</th>
            <th width="10%" bgcolor="#eee">صافي الراتب</th>
            <th width="20%" bgcolor="#eee">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($salaries as $salary) {
      $table_content .= '<tr>
              <td width="10%">' . $i . '</td>
              <td width="20%">' . $salary->date_created . '</td>
              <td width="20%">' . $salary->name . '</td>
              <td width="10%">' . $salary->remaining_balance . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $salary->balance . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $salary->net_balance . '<span>&#8362;&#160;</span></td>
              <td width="20%">' . $salary->notes . '</td>
            </tr>';
      $total += $salary->net_balance;
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل الرواتب');
    PDF::SetAuthor('اياد الهسي');
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

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $total . '<span>&#8362;&#160;</span></td></tr></tbody></table>');
    PDF::Output('all_workers_' . date('ymdhis') . '.pdf', 'I');
    return response()->json(['status' => 'success']);
  }
}
