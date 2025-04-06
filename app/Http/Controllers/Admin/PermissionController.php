<?php

namespace App\Http\Controllers\Admin;

use Exception;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  // create
  public function create()
  {
    $modal = view('admin.permission.create')->render();
    return response()->json(['status' => 'success', 'modal' => $modal]);
  }

  // index
  public function index(Request $request)
  {
    $page = config('app.page');
    if ($request->ajax()) {
      $permissions = Permission::select('id', 'name', 'description')->orderBy('id', 'DESC')->paginate($page);
      $table = view('admin.permission.table', compact('permissions'))->render();
      return response()->json(['table' => $table]);
    } else {
      $permissions = Permission::select('id', 'name', 'description')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Permission::count() / $page);
      return view('admin.permission.index', compact('permissions', 'pages'));
    }
  }

  // store
  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      $this->validate($request, [
        'name' => 'required|unique:permissions,name',
        'description' => 'required'
      ]);

      $permission = new Permission();
      $permission->name = $request->name;
      $permission->description = $request->description;
      $permission->save();

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollback();
      return response()->json(['status' => 'error']);
    }
  }

  // edit
  public function edit(Request $request, $id)
  {
    $permission = Permission::select('id', 'name', 'description')->where('id', $id)->first();
    return view('admin.permission.edit', compact('permission'));
  }

  // updateOne
  public function updateOne(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|unique:permissions,name,' . $request->id,
      'description' => 'required'
    ]);
    DB::beginTransaction();
    try {
      $permission = Permission::find($request->id);
      if ($permission != null) {
        $permission->name = $request->name;
        $permission->description = $request->description;
        $permission->save();
        DB::commit();
        return redirect('/permissions')->with('success', 'تمت تحديث الصلاحية بنجاح');
        // return response()->json(['status' => 'success', 'message', 'تمت تحديث الصلاحية بنجاح']);
      }
      DB::rollback();
      return redirect('/permissions')->with('error', 'عذرا الصلاحية غير موجودة!');
      // return response()->json(['status' => 'error', 'message' => 'عذرا الصلاحية غير موجودة!']);
    } catch (Exception $e) {
      DB::rollback();
      return redirect('/permissions')->with('error', 'حدث خطا اثناء عملية الاضافة!');
      // return response()->json(['status' => 'error', 'message' => 'حدث خطا اثناء عملية الاضافة!']);
      // return dd($e->getMessage());
    }
  }

  // upgrade
  public function upgrade(Request $request)
  {
    DB::beginTransaction();
    try {
      $permissions = Permission::select('id', 'name', 'description')->orderBy('id', 'DESC')->get();
      $users = User::select('id', 'name')->orderBy('id', 'DESC')->get();
      DB::commit();
      return view('admin.permission.upgrade', compact('permissions', 'users'));
    } catch (Exception $e) {
      DB::rollback();
      return response()->json(['status' => 'error']);
    }
  }

  // update
  public function grant(Request $request)
  {
    DB::beginTransaction();
    try {

      $id = Auth::user()->id;
      $user_id = $request->user_id;
      if ($id == 1) {
        $user = User::where('id', $user_id)->first();

        if ($user != null) {
          $user->syncPermissions($request->permissions);

          DB::commit();
          return redirect('/permissions')->with('success', 'تمت تحديث الصلاحيات بنجاح');
          // return response()->json(['status' => 'success', 'message' => 'تمت تحديث الصلاحيات بنجاح']);
        }
      }
      return redirect('/permissions')->with('error', 'عذرا المستخدم غير موجود!');
      // return response()->json(['status' => 'error', 'message' => 'عذرا المستخدم غير موجود!']);
    } catch (Exception $e) {
      DB::rollback();
      return redirect('/permissions')->with('error', 'حدث خطا اثناء عملية الاضافة!');
      // return response()->json(['status' => 'error', 'message' => 'حدث خطا اثناء عملية الاضافة!']);
    }
  }
}
