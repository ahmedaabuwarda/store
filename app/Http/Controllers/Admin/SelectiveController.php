<?php

namespace App\Http\Controllers;

use App\Models\Selective;
use Illuminate\Http\Request;

class SelectiveController extends Controller
{
  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  // index
  public function index(Request $request)
  {
    $page = config('app.page');
    if ($request->ajax()) {
      $movements = Selective::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $table = view('admin.movement.table', compact('movements'))->render();
      return response()->json(['table' => $table]);
    } else {
      $movements = Selective::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Selective::count() / $page);
      return view('admin.movement.index', compact('movements', 'pages'));
    }
  }
}
