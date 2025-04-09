<?php

namespace App\Http\Controllers\Admin;

use Exception;

use App\Models\Movement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class MovementController extends Controller
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
      $movements = Movement::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $table = view('admin.movement.table', compact('movements'))->render();
      return response()->json(['table' => $table]);
    } else {
      $movements = Movement::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Movement::count() / $page);
      return view('admin.movement.index', compact('movements', 'pages'));
    }
  }
}
