<?php

namespace App\Http\Controllers\Admin;

use Exception;

use App\Models\Currency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
  //
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $page = config('app.page');
    if ($request->ajax()) {
      $currencies = Currency::select('id', 'name', 'symbol')->orderBy('id', 'DESC')->paginate($page);
      $table = view('admin.currency.table', compact('currencies'))->render();
      return response()->json(['table' => $table]);
    } else {
      $currencies = Currency::select('id', 'name', 'symbol')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Currency::count() / $page);
      return view('admin.currency.index', compact('currencies', 'pages'));
    }
  }

  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      $currency = new Currency();
      $currency->name = $request->name;
      $currency->symbol = $request->symbol;
      $currency->save();

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      // return response()->json(['status' => 'error']);
      // return dd($e->getMessage());
      return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
    }
  }

  public function edit(Request $request, $id)
  {
    $currency = Currency::where('id', $id)->select('id', 'name', 'symbol')->first();
    return view('admin.currency.edit', compact('currency'));
  }

  public function update(Request $request)
  {
    $id = $request->id;
    $name = $request->name;
    $symbol = $request->symbol;

    DB::beginTransaction();
    try {

      $currency = Currency::where('id', $id)->first();
      $currency->update([
        'name' => $name,
        'symbol' => $symbol,
      ]);

      DB::commit();
      return redirect('/currencies')->with('success', 'تم تحديث العملة بنجاح');
    } catch (Exception $e) {
      DB::rollBack();
      // return $e->getMessage();
      // return response()->json(['status' => 'error']);
      return redirect('/currencies')->with('error', 'حدث خطأ أثناء تحديث العملة');
    }
  }
}
