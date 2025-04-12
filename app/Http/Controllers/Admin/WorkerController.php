<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Worker;
use App\Models\Box;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class WorkerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = config('app.page');
        $boxes = Box::select('id', 'name', 'balance')->get();
        // if the request is ajax
        if($request->ajax()){
            $workers = Worker::select('id', 'name', 'balance', 'notes', 'status')->orderBy('created_at', 'DESC')->paginate($page);
            $salaries = DB::select('SELECT salaries.id, salaries.worker_id, salaries.remaining_balance, salaries.balance, salaries.net_balance, salaries.date_created, salaries.notes, workers.name, workers.id FROM salaries, workers WHERE salaries.worker_id = workers.id ORDER BY salaries.date_created DESC LIMIT 20');

            $table = view('admin.worker.table', compact('workers'))->render();
            $salaries_table = view('admin.worker.salaries_table', compact('salaries'))->render();

            return response()->json(['table' => $table, 'salaries_table' => $salaries_table]);
        // if the request is not ajax
        } else {
            $workers = Worker::select('id', 'name', 'balance', 'notes', 'status')->orderBy('created_at', 'DESC')->paginate($page);
            $salaries = DB::select('SELECT salaries.id, salaries.worker_id, salaries.remaining_balance, salaries.balance, salaries.net_balance, salaries.date_created, salaries.notes, workers.name, workers.id FROM salaries, workers WHERE salaries.worker_id = workers.id ORDER BY salaries.date_created DESC LIMIT 20');
            $pages = ceil(Worker::count()/$page);

            return view('admin.worker.index', compact('workers', 'salaries', 'pages', 'boxes'));
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $worker = new Worker;
            $worker->name = $request['name'];
            $worker->balance = 0;
            if ($request['notes'] == null) {
                $worker->notes = 'لا يوجد';
            } else {
                $worker->notes = $request['notes'];
            }
            $worker->save();

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error']);
        }
    }

    public function to_pdf(Request $request)
    {
        $from = $request['from'];
        $to = $request['to'];
        $workers = DB::select('SELECT name, balance, notes, status FROM workers WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف كل الموظفين</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
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
        foreach($workers as $worker) {
            $status = ''; $balance = '';
            if ($worker->status == 1) {
                $status = 'موجود';
            } else {
                $status = 'غير موجود';
            }
            if($worker->balance > 0) {
                $balance = $worker->balance.'<span>&#8362;&#160;</span> - دائن -';
            } elseif($worker->balance < 0) {
                $balance = $worker->balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $balance = $worker->balance.'<span>&#8362;&#160;</span>';
            }
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="30%">'.$worker->name.'</td>
              <td width="20%">'.$balance.'</td>
              <td width="20%">'.$worker->notes.'</td>
              <td width="20%">'.$status.'</td>
            </tr>';
            $total += $worker->balance; $i++;
        }
        if($total < 0) {
            $total = $total.'<span>&#8362;&#160;</span> - مدين -';
        } elseif($total > 0) {
            $total = $total.'<span>&#8362;&#160;</span> - دائن -';
        } else {
            $total = $total.'<span>&#8362;&#160;</span>';
        }

        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل الموظفين');
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
        PDF::Output('all_workers_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }

    public function kashf_to_pdf(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $id = $request->id;

        $worker = DB::select('SELECT name, balance FROM workers WHERE id = :id', ['id' => $id]);
        $worker_sarf = DB::select('SELECT sanadat_sarfs.date_created, sanadat_sarfs.number, sanadat_sarfs.balance, sanadat_sarfs.byan FROM workers, sanadat_sarfs WHERE workers.id = sanadat_sarfs.worker_id AND workers.id = :id AND sanadat_sarfs.date_created >= :from AND sanadat_sarfs.date_created <= :to ORDER BY sanadat_sarfs.id DESC', ['id' => $id, 'from' => $from, 'to' => $to]);

        $worker_qapd = DB::select('SELECT sanadat_qapds.date_created, sanadat_qapds.number, sanadat_qapds.balance, sanadat_qapds.byan FROM workers, sanadat_qapds WHERE workers.id = sanadat_qapds.worker_id AND workers.id = :id AND sanadat_qapds.date_created >= :from AND sanadat_qapds.date_created <= :to ORDER BY sanadat_qapds.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $worker_buy = DB::select('SELECT buy_bills.date_created, buy_bills.number, buy_bills.paid_balance, buy_bills.byan, buy_bills.remaining_balance FROM workers, buy_bills WHERE workers.id = buy_bills.worker_id AND workers.id = :id AND buy_bills.date_created >= :from AND buy_bills.date_created <= :to ORDER BY buy_bills.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $worker_sell = DB::select('SELECT export_ainiats.date_created, export_ainiats.number, export_ainiats.paid_balance, export_ainiats.byan, export_ainiats.remaining_balance FROM workers, export_ainiats WHERE workers.id = export_ainiats.worker_id AND workers.id = :id AND export_ainiats.date_created >= :from AND export_ainiats.date_created <= :to ORDER BY export_ainiats.id DESC;', ['id' => $id, 'from' => $from, 'to' => $to]);

        $worker_salary = DB::select('SELECT salaries.id, salaries.worker_id, salaries.remaining_balance, salaries.balance, salaries.net_balance, salaries.date_created, salaries.notes, workers.name, workers.id FROM salaries, workers WHERE salaries.worker_id = ? AND workers.id = ? AND salaries.date_created >= ? AND salaries.date_created <= ? ORDER BY salaries.date_created DESC', [$id, $id, $from, $to]);

        $i = 1; $sarf_total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف حساب</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'&#160;&#160;&#160;&#160;الاسم: '.$worker[0]->name.' - موظف</p></br>';
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
        foreach($worker_sarf as $sanadat_sarf) {
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
        foreach($worker_qapd as $sanadat_qapd) {
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
        foreach($worker_buy as $buy_bill) {
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
        foreach($worker_sell as $export_ainiat) {
            $remaining = '';
            if($export_ainiat->remaining_balance > 0) {
                $remaining = $export_ainiat->remaining_balance.'<span>&#8362;&#160;</span> - دائن -';
            } else if ($export_ainiat->remaining_balance < 0) {
                $remaining = $export_ainiat->remaining_balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $remaining = $remaining = $export_ainiat->remaining_balance.'<span>&#8362;&#160;</span>';
            }
            $sell_table .= '<tr>
              <td width="5%">'.$i.'</td>
              <td width="20%">'.$export_ainiat->number.'</td>
              <td width="20%">'.$export_ainiat->date_created.'</td>
              <td width="15%">'.$export_ainiat->paid_balance.'<span>&#8362;&#160;</span></td>
              <td width="20%">'.$remaining.'</td>
              <td width="20%">'.$export_ainiat->byan.'</td>
            </tr>';
            $sell_total += $export_ainiat->remaining_balance; $i++;
        }
        if ($sell_total > 0) {
            $sell_total = $sell_total.'<span>&#8362;&#160;</span> - دائن -';
        } else if ($sell_total < 0) {
            $sell_total = $sell_total.'<span>&#8362;&#160;</span> - مدين -';
        } else {
            $sell_total = $sell_total.'<span>&#8362;&#160;</span>';
        }

        $sell_table .= '</tbody></table>';

        $i = 1; $salary_total = 0;
        $salary_table = '<h2>الرواتب</h2></br><table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead class="thead-light">
            <tr>
                <th width="5%" bgcolor="#eee">#</th>
                <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
                <th width="20%" bgcolor="#eee">المستهلك</th>
                <th width="20%" bgcolor="#eee">رصيد متبقي</th>
                <th width="15%" bgcolor="#eee">راتب اساسي</th>
                <th width="10%" bgcolor="#eee">صافي الراتب</th>
                <th width="10%" bgcolor="#eee">ملاحظات</th>
            </tr>
        </thead>
        <tbody>';
        foreach($worker_salary as $salary) {
            $remaining = '';
            if($salary->remaining_balance > 0) {
                $remaining = $salary->remaining_balance.'<span>&#8362;&#160;</span> - دائن -';
            } else if ($salary->remaining_balance < 0) {
                $remaining = $salary->remaining_balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $remaining = $remaining = $salary->remaining_balance.'<span>&#8362;&#160;</span>';
            }
            $salary_table .= '<tr>
              <td width="5%">'.$i.'</td>
              <td width="20%">'.$salary->date_created.'</td>
              <td width="20%">'.$salary->name.'</td>
              <td width="20%">'.$remaining.'</td>
              <td width="15%">'.$salary->balance.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$salary->net_balance.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$salary->notes.'</td>
            </tr>';
            $salary_total += $salary->net_balance; $i++;
        }
        if ($salary_total > 0) {
            $salary_total = $salary_total.'<span>&#8362;&#160;</span> - دائن -';
        } else if ($salary_total < 0) {
            $salary_total = $salary_total.'<span>&#8362;&#160;</span> - مدين -';
        } else {
            $salary_total = $salary_total.'<span>&#8362;&#160;</span>';
        }

        $salary_table .= '</tbody></table>';

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

        PDF::writeHTML($salary_table);
        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$salary_total.'</td></tr></tbody></table>');

        $balance = '';
        if ($worker[0]->balance > 0) {
            $balance = $worker[0]->balance.'<span>&#8362;&#160;</span> - دائن -';
        } else if ($worker[0]->balance < 0) {
            $balance = $worker[0]->balance.'<span>&#8362;&#160;</span> - مدين -';
        } else {
            $balance = $worker[0]->balance.'<span>&#8362;&#160;</span>';
        }

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">الرصيد</td><td width="20%">'.$balance.'</td></tr></tbody></table>');

        PDF::Output('worker_kashf_hisab_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
