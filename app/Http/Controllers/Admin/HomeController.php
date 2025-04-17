<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\ExportAiniat;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

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

    $totals = DB::select('SELECT sum( box.counter ) AS dump,
        ( SELECT sum( products.quantity ) FROM products ) AS total_products_count,
        ( SELECT sum( expenses.balance ) FROM expenses ) AS total_expenses,
        ( SELECT sum( box.balance ) FROM box WHERE box.name LIKE :shekel) AS total_shekel,
        ( SELECT sum( box.balance ) FROM box WHERE box.name LIKE :dollar) AS total_dollar,
        ( SELECT sum( box.balance ) FROM box WHERE box.name LIKE :dinar) AS total_dinar
        FROM box;', ['shekel' => '%شيكل%', 'dollar' => '%دولار%', 'dinar' => '%دينار%']);
    // $totals = null;
    $products = Product::select('id', 'name', 'quantity', 'original_quantity', 'status', 'type', 'created_at')->orderBy('quantity', 'ASC')->paginate($page);

    if ($request->ajax()) {

      $table = view('website.table', compact('products'))->render();
      $statistics = view('includes.statistics', compact('totals'))->render();
      return response()->json(['table' => $table, 'statistics' => $statistics]);
    } else {
      $pages = ceil(Product::count() / $page);
      return view('website.home', compact('products', 'totals', 'pages'));
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

    return redirect('/home')->with('success', 'تم نحديث الملاحظاتات بنجاح');
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
      $result = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'mosque_id', 'notes', 'status', 'created_at')
      ->with('mosque:id,name')
      ->where('name', 'like', '%' . $search_query . '%')
      ->orderBy('id', 'DESC')
      ->paginate($page);
    } else if ($target == 'selectives') {
      $result = Customer::select('id', 'name', 'identity', 'phone', 'family_number', 'mosque_id', 'notes', 'status', 'created_at')
        ->with('mosque:id,name')
        ->where('name', 'like', '%' . $search_query . '%')
        ->where('status', false)
        ->orderBy('id', 'DESC')
        ->paginate($page);
    } else if ($target == 'products') {
      $result = Product::select('id', 'name', 'quantity', 'original_quantity', 'original_price', 'taqseet_price', 'status', 'type')->where('name', 'like', '%' . $search_query . '%')->orderBy('id', 'DESC')->paginate($page);
    } else if ($target == 'export_ainiats') {
      $result = ExportAiniat::where('number', date($search_query))->orderBy('id', 'DESC')->get();
    }
    $pages = ceil(count($result) / $page);
    return view('website.search', compact('result', 'pages', 'target'));
  }
}
