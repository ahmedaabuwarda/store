<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use App\Http\Requests\DiscountStoreRequest;
use DB;
use PDF;

class DiscountAndExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $page = config('app.page');
        // if the request is ajax
        if($request->ajax()){
            $discounts = Discount::select('date_created', 'balance', 'notes')->orderBy('date_created', 'DESC')->paginate($page);
            $table = view('admin.discount.table', compact('discounts'))->render();
            return response()->json(['table' => $table]);
        // if the request is not ajax
        } else {
            $discounts = Discount::select('date_created', 'balance', 'notes')->orderBy('date_created', 'DESC')->paginate($page);
            $pages = ceil(Discount::count()/$page);
            return view('admin.discount.index', compact('discounts', 'pages'));
        }
    }
    public function store(DiscountStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $balance = abs($request->balance);
            $discount = new Discount;
            $discount->balance = $balance;
            $discount->date_created = $request->date_created;
            if($request->notes == null){
                $discount->notes = 'لا يوجد';
            } else {
                $discount->notes = $request->notes;
            }
            $discount->save();

            DB::statement('UPDATE box SET box.remaining = CASE box.id 
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
                WHEN 2 THEN (SELECT remaining FROM box WHERE box.id = 2)+?
                ELSE box.remaining
                END,
            box.counter = CASE box.id 
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                WHEN 2 THEN (SELECT counter FROM box WHERE box.id = 2)+1 
                ELSE box.counter
                END
            WHERE box.id IN(1, 2);', [$balance, $balance]);

            $date = date($request['date_created'].' H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,0,?,?)',[$balance,'مصروف', $date]);

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error']);
        }
    }
    public function to_pdf(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $discounts = DB::select('SELECT date_created, balance, notes from discounts where date_created >= :from AND date_created <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = \Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف كل المصاريف</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'<p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%">الرقم</th>
            <th width="40%">تاريخ الانشاء</th>
            <th width="10%">المبلغ</th>
            <th width="40%">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
        foreach($discounts as $discount) {
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="40%">'.$discount->date_created.'</td>
              <td width="10%">'.$discount->balance.'<span>&#8362;&#160;</span></td>
              <td width="40%">'.$discount->notes.'</td>
            </tr>';
            $total += $discount->balance; $i++;
        }
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل المصاريف');
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
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

        PDF::AddPage();
        PDF::writeHTML($content);
        PDF::SetFont('freeserif', '', 11);
        PDF::writeHTML($table_content);

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%"></td><td width="40%">المجموع</td><td width="10%">'.$total.'<span>&#8362;&#160;</span></td></tr></tbody></table>');
        PDF::Output('all_discounts.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
