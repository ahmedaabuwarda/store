<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use Illuminate\Http\Request;

class MosqueController extends Controller
{

  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $page = config('app.page');
    if ($request->ajax()) {
      $movements = Mosque::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $table = view('admin.movement.table', compact('movements'))->render();
      return response()->json(['table' => $table]);
    } else {
      $movements = Mosque::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Mosque::count() / $page);
      return view('admin.movement.index', compact('movements', 'pages'));
    }
  }
}
