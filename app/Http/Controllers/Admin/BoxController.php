<?php

namespace App\Http\Controllers\Admin;

use Exception;

use App\Models\Box;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{
  //
  public function __construct()
  {
    $this->middleware('auth');
  }

  // index
  public function index(Request $request)
  {
    $page = config('app.page');
    $currencies = Currency::select('id', 'name')->orderBy('id', 'DESC')->get();

    if ($request->ajax()) {
      $boxes = Box::select('id', 'name', 'currency_id', 'balance')->with('currency')->orderBy('id', 'DESC')->paginate($page);
      $table = view('admin.box.table', compact('boxes'))->render();
      return response()->json(['table' => $table]);
    } else {
      $boxes = Box::select('id', 'name', 'currency_id', 'balance')->with('currency')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Box::count() / $page);
      return view('admin.box.index', compact('boxes', 'pages', 'currencies'));
    }
  }

  // store
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'currency_id' => 'required|exists:currencies,id',
    ]);

    DB::beginTransaction();
    try {

      $box = new Box();
      $box->name = $request->name;
      $box->currency_id = $request->currency_id;
      $box->balance = 0;
      $box->save();

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      // return response()->json(['status' => 'error']);
      // return dd($e->getMessage());
      return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
    }
  }

  // edit
  public function edit($id)
  {
    $box = Box::select('id', 'name', 'currency_id', 'balance')->where('id', $id)->with('currency')->first();
    $currencies = Currency::select('id', 'name', 'symbol')->get();
    return view('admin.box.edit', compact('box', 'currencies'));
  }

  // update
  public function update(Request $request)
  {

    $request->validate([
      'id' => 'required|numeric',
      'name' => 'required|string|min:5',
      'currency_id' => 'required|numeric',
    ]);

    $box = Box::select('id', 'name', 'currency_id')->where('id', $request->id)->first();

    $box->update([
      'name' => $request->name,
      'currency_id' => $request->currency_id,
    ]);

    return redirect('/boxes')->with('success', 'تم تحديث الصندوق بنجاح');
  }

  // convert
  public function convert(Request $request)
  {
    // dd($request);
    $request->validate([
      'box_from' => 'required',
      'box_to' => 'required',
      'balance' => 'required|min:0',
      'convert_price' => 'required|min:0',
      'operation' => 'required',
    ]);

    $box_from = $request->box_from;
    $box_to = $request->box_to;
    $balance = $request->balance;
    $convert_price = $request->convert_price;
    $operation = $request->operation;
    $user_id = Auth::user()->id;

    DB::beginTransaction();
    try {

      $box_from = Box::select('id', 'name', 'currency_id', 'balance')->where('id', $box_from)->first();
      $box_to = Box::select('id', 'name', 'currency_id', 'balance')->where('id', $box_to)->first();

      if ($box_from->balance < $balance) {
        return response()->json(['status' => 'error', 'error' => 'لا يوجد لديك رصيد كاف!']);
      }
      $box_from->update([
        'balance' => $box_from->balance - $balance,
      ]);
      $original_balance = $balance;
      if ($operation == '*') {
        $balance = $balance * $convert_price;
      } else {
        $balance = $balance / $convert_price;
      }
      $box_to->update([
        'balance' => $box_to->balance + $balance,
      ]);

      $date = date('Y-m-d H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,0,?,?,?,?)', [$original_balance, 'تحويل من صندوق - '.$box_from->name, $date, $box_from->id, $user_id]);
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,1,?,?,?,?)', [$balance, 'تحويل الى صندوق - '.$box_to->name, $date, $box_to->id, $user_id]);

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      // return response()->json(['status' => 'error']);
      // return dd($e->getMessage());
      return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
    }
  }
}
