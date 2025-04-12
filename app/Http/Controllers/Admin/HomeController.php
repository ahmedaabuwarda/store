<?php

namespace App\Http\Controllers\Admin;

use PDF;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\ExportAiniat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $page = config('app.page');
    $date = date('Y-m-d H:i:s');

    $productsCount = DB::select('SELECT sum( box.counter ) AS dump,
        ( SELECT sum( export_ainiats.total_profit ) FROM export_ainiats WHERE date_created = :date ) AS daily_profit,
        ( SELECT sum( products.quantity ) FROM products WHERE products.name = "رصيد وطنية") AS ooredoo_balance,
        ( SELECT sum( products.quantity ) FROM products ) AS total_products_count,
        ( SELECT sum( quantities.quantity * quantities.buy_price ) FROM quantities) AS total_cost_price,
        ( SELECT sum( customers.balance ) FROM customers WHERE customers.balance <= 0) AS needFromPeople1,
        ( SELECT sum( customers.balance ) FROM customers WHERE customers.balance >= 0) AS peopleNeedFromMe1,
        ( SELECT sum( providers.balance ) FROM providers WHERE providers.balance <= 0) AS needFromPeople2,
        ( SELECT sum( providers.balance ) FROM providers WHERE providers.balance >= 0) AS peopleNeedFromMe2
        FROM box, export_ainiats;', ['date' => date('Y-m-d')]);

    $box = DB::select('SELECT remaining, counter FROM box');

    $products = Product::select('id', 'name', 'quantity', 'original_quantity', 'original_price', 'taqseet_price', 'status', 'type')->orderBy('quantity', 'ASC')->paginate($page);

    if ($request->ajax()) {

      $table = view('website.table', compact('products'))->render();
      $statistics = view('includes.statistics', compact('box', 'productsCount'))->render();
      return response()->json(['table' => $table, 'statistics' => $statistics]);
    } else {
      if ($box == null) {
        $box = DB::select('SELECT remaining, counter FROM box');
      }

      $movements = DB::select('SELECT movements.balance, movements.type, movements.from, movements.date_created FROM movements ORDER BY movements.id DESC LIMIT 20');
      $pages = ceil(Product::count() / $page);
      return view('website.home', compact('products', 'productsCount', 'pages', 'box', 'movements'));
    }
  }

  // settings
  public function settings(Request $request)
  {
    $user = Auth::user();
    return view('admin.user.edit', compact('user'));
  }

  // updateUser
  public function update_user(Request $request)
  {

    $name = $request->name;
    $email = $request->email;
    $password = $request->password;

    if ($name == null || $email == null || $password == null) {
      return redirect()->with('error', 'بعض الحقول مفقودة!');
    }

    $user = User::find(Auth::user()->id);
    $user->update([
      'name' => $name,
      'email' => $email,
      'password' => Hash::make($password)
    ]);

    return redirect('/home')->with('success', 'تم نحديث البيانات بنجاح');
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
      $result = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'balance', 'notes', 'status', 'created_at')->where('name', 'like', '%' . $search_query . '%')->orderBy('id', 'DESC')->paginate($page);
    } else if ($target == 'products') {
      $result = Product::select('id', 'name', 'quantity', 'original_quantity', 'original_price', 'taqseet_price', 'status', 'type')->where('name', 'like', '%' . $search_query . '%')->orderBy('id', 'DESC')->paginate($page);
    } else if ($target == 'export_ainiats') {
      $result = ExportAiniat::where('date_created', date($search_query))->orderBy('id', 'DESC')->get();
    }
    $pages = ceil(count($result) / $page);
    return view('website.search', compact('result', 'pages', 'target'));
  }
}
