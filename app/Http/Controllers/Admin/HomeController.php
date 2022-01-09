<?php

namespace App\Http\Controllers\Admin;

use PDF;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\SellBill;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Spatie\Permission\Models\Permission;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $permissions = Permission::all();
        if ($permissions->isEmpty()) {
            Permission::create(['name' => 'add_to_box']);
            Permission::create(['name' => 'add_buy_bills']);
            Permission::create(['name' => 'add_customers']);
            Permission::create(['name' => 'add_discounts']);
            Permission::create(['name' => 'add_products']);
            Permission::create(['name' => 'add_providers']);
            Permission::create(['name' => 'add_salaries']);
            Permission::create(['name' => 'add_sanadat_qapds']);
            Permission::create(['name' => 'add_sanadat_sarfs']);
            Permission::create(['name' => 'add_sell_bills']);
            Permission::create(['name' => 'add_workers']);
        }
        $page = config('app.page');
        $date = date('Y-m-d H:i:s');

        $productsCount = DB::select('SELECT sum( box.counter ) AS dump,
        ( SELECT sum( sell_bills.total_profit ) FROM sell_bills WHERE date_created = :date ) AS daily_profit,
        ( SELECT sum( products.quantity ) FROM products WHERE products.name = "رصيد جوال") AS jawwal_balance,
        ( SELECT sum( products.quantity ) FROM products WHERE products.name = "رصيد وطنية") AS ooredoo_balance,
        ( SELECT sum( products.quantity ) FROM products ) AS total_products_count,
        ( SELECT sum( products.quantity * products.original_price ) FROM products) AS total_cost_price,
        ( SELECT sum( customers.balance ) FROM customers WHERE customers.balance <= 0) AS needFromPeople1,
        ( SELECT sum( customers.balance ) FROM customers WHERE customers.balance >= 0) AS peopleNeedFromMe1,
        ( SELECT sum( providers.balance ) FROM providers WHERE providers.balance <= 0) AS needFromPeople2,
        ( SELECT sum( providers.balance ) FROM providers WHERE providers.balance >= 0) AS peopleNeedFromMe2
        FROM box, sell_bills;', ['date' => date('Y-m-d')]);

        $box = DB::select('SELECT remaining, counter FROM box');

        $products = Product::select('id', 'name', 'quantity', 'original_quantity', 'original_price', 'status', 'type')->orderBy('quantity', 'ASC')->paginate($page);

        if ($request->ajax()) {

            $table = view('website.table', compact('products'))->render();
            $statistics = view('includes.statistics', compact('box', 'productsCount'))->render();
            return response()->json(['table' => $table, 'statistics' => $statistics]);

        } else {
            if ($box == null) {

                DB::insert('INSERT INTO box (remaining, counter, created_at, updated_at) VALUES (0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?),(0, 0, ?, ?)', [$date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date, $date]);
                $box = DB::select('SELECT remaining, counter FROM box');

            }

            $movements = DB::select('SELECT movements.balance, movements.type, movements.from, movements.date_created FROM movements ORDER BY movements.id DESC LIMIT 20');
            $pages = ceil($box[2]->counter / $page);
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
        } else if ($target == 'products') {
            $result = Product::select('id', 'name', 'quantity', 'original_quantity', 'original_price', 'status', 'type')->where('name', 'like', '%' . $search_query . '%')->orderBy('id', 'DESC')->paginate($page);
        } else if ($target == 'sell_bills') {
            $result = SellBill::where('date_created', date($search_query))->orderBy('id', 'DESC')->get();
        }
        $pages = ceil(count($result) / $page);
        return view('website.search', compact('result', 'pages', 'target'));

    }

    public function box_store(Request $request)
    {

        $balance = abs($request->balance);
        DB::beginTransaction();
        try {

            DB::update('UPDATE box SET remaining = (SELECT remaining FROM box WHERE id = 1)+?, counter = (SELECT counter FROM box WHERE id = 1)+1 WHERE id = 1;', [$balance]);

            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,?,?,?)', [$balance, true, 'دخل الصندوق', date('Y-m-d H:i:s')]);

            DB::commit();
            return response(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'error']);
        }

    }

    public function to_pdf(Request $request)
    {
        $from = date($request['from'] . ' H:i:s');
        $to = date($request['to'] . ' H:i:s');

        $movements = DB::select('SELECT movements.balance, movements.type, movements.from, movements.date_created FROM movements WHERE movements.date_created >= :from AND movements.date_created <= :to ORDER BY movements.id DESC', ['from' => $from, 'to' => $to]);

        $i = 1;
        $total = 0;
        $time = date('H:i:s');
        $date = date('Y-m-d');
        $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف كل حركات الصندوق</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="25%" bgcolor="#eee">الرقم</th>
            <th width="25%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="25%" bgcolor="#eee">المبلغ</th>
            <th width="25%" bgcolor="#eee">من</th>
          </tr>
        </thead>
        <tbody>';
        foreach ($movements as $movement) {
            $balance = '';
            if ($movement->type == 0) {
                $balance = $movement->balance . '<span>&#8362;&#160;</span> - خارج';
                $total -= $movement->balance;
            } else {
                $balance = $movement->balance . '<span>&#8362;&#160;</span> - داخل';
                $total += $movement->balance;
            }

            $table_content .= '<tr>
              <td width="25%">' . $i . '</td>
              <td width="25%">' . $movement->date_created . '</td>
              <td width="25%">' . $balance . '</td>
              <td width="25%">' . $movement->from . '</td>
            </tr>';
            $i++;
        }
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل حركات الصندوق');
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%" color="#fff" bgcolor="#003B36">' . $total . '<span>&#8362;&#160;</span></td></tr></tbody></table>');
        PDF::Output('all_box_movements_' . date('ymdhis') . '.pdf', 'I');
        return response()->json(['status' => 'success']);
    }

}