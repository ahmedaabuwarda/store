<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellBill;
use App\Models\BuyBill;
use App\Models\Sanadat_Qapd;
use App\Models\Sanadat_Sarf;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Customer;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;
use PDF;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $page = config('app.page');
        if($request->ajax()){
            $products = Product::select('id','name','quantity','original_quantity','original_price','status','type')->orderBy('id','DESC')->paginate($page);

            $box = DB::select('SELECT remaining, counter FROM box WHERE box.id IN (1,2,3,4,5,6,7)');
            $productsCount = DB::select('SELECT sum(sold_products.quantity) as total_soledProducts_quantity, (SELECT count(products.quantity) FROM products) as products_count, (SELECT sum(products.quantity) FROM products) as total_products_count, (SELECT sum(products.quantity * products.original_price) FROM products WHERE products.original_price > 0) AS total_cost_price FROM sold_products;');

            $table = view('website.table', compact('products'))->render();
            $statistics = view('includes.statistics', compact('box', 'productsCount'))->render();

            return response()->json(['table' => $table, 'statistics' => $statistics]);
        } else {
            $box = DB::select('SELECT remaining FROM box WHERE id = 1');
            if ($box == null) {
                $date = date('Y-m-d H:i:s');
                DB::insert('INSERT INTO box (remaining, counter, created_at, updated_at) VALUES (0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?)', [ $date,$date,$date,$date,$date,$date,$date,$date,$date,$date,$date,$date,$date,$date ]);
            }

            $productsCount = DB::select('SELECT sum(sold_products.quantity) as total_soledProducts_quantity, (SELECT count(products.quantity) FROM products) as products_count, (SELECT sum(products.quantity) FROM products) as total_products_count, (SELECT sum(products.quantity * products.original_price) FROM products WHERE products.original_price > 0) AS total_cost_price FROM sold_products;');

            $box = DB::select('SELECT remaining, counter FROM box WHERE box.id IN (1,2,3,4,5,6,7)');
            $movements = DB::select('SELECT movements.balance, movements.type, movements.from, movements.date_created FROM movements ORDER BY movements.id DESC LIMIT 20');

            $products = Product::select('id','name','quantity','original_quantity','original_price','status','type')->orderBy('id','DESC')->paginate($page);

            $pages = ceil($productsCount[0]->products_count/$page);

            return view('website.home', compact('products', 'productsCount', 'pages', 'box', 'movements'));
        }
    }
    public function search(Request $request)
    {
        $page = config('app.page');
        $search_query = $request->search_field;
        $target = $request->target;
        $result = '';
        if ($search_query == null) {
            return redirect()->back();
        }
        if ($target == 'providers') {
            $result = Provider::select('id', 'name', 'balance', 'notes', 'status')->where('name', 'like', '%' . $search_query . '%')->orderBy('id', 'DESC')->paginate($page);
        } else if ($target == 'customers') {
            $result = Customer::select('id', 'name', 'balance', 'notes', 'status')->where('name', 'like', '%' . $search_query . '%')->orderBy('id', 'DESC')->paginate($page);
        }
        $pages = ceil(Provider::count()/$page);
        return view('website.search', compact('result', 'pages', 'target'));
    }
    public function to_pdf(Request $request)
    {
        $from = date($request['from'].' H:i:s');
        $to = date($request['to'].' H:i:s');

        $movements = DB::select('SELECT movements.balance, movements.type, movements.from, movements.date_created FROM movements WHERE movements.date_created >= :from AND movements.date_created <= :to ORDER BY movements.id DESC', [ 'from' => $from, 'to' => $to ]);

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = \Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف كل حركات الصندوق</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="25%">الرقم</th>
            <th width="25%">تاريخ الانشاء</th>
            <th width="25%">المبلغ</th>
            <th width="25%">من</th>
          </tr>
        </thead>
        <tbody>';
        foreach($movements as $movement) {
            $balance = '';
            if ($movement->type == 0) {
                $balance = $movement->balance.'<span>&#8362;&#160;</span> - خارج';
                $total -= $movement->balance;
            } else {
                $balance = $movement->balance.'<span>&#8362;&#160;</span> - داخل';
                $total += $movement->balance;
            }

            $table_content .= '<tr>
              <td width="25%">'.$i.'</td>
              <td width="25%">'.$movement->date_created.'</td>
              <td width="25%">'.$balance.'</td>
              <td width="25%">'.$movement->from.'</td>
            </tr>';
            $i++;
        }
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل حركات الصندوق');
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
        PDF::Output('all_box_movements_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
