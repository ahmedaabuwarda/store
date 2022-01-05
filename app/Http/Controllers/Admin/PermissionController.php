<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    // auth
    public function __construct ()
    {
        $this->middleware('auth');
    }

    // create
    public function create ()
    {
        $modal = view('admin.permission.create')->render();
        return response()->json(['status' => 'success', 'modal' => $modal]);
    }

    // update
    public function update (Request $request) 
    {
        DB::beginTransaction();
        try {

            $id = Auth::user()->id;
            if ($id == 1) {
                $permissions = ['add_to_box', 'add_buy_bills', 'add_customers', 'add_discounts', 'add_products', 'add_providers', 'add_salaries', 'add_sanadat_qapds', 'add_sanadat_sarfs', 'add_sell_bills', 'add_workers'];
            } else if ($id != 1) {
                $permissions = ['add_customers', 'add_discounts', 'add_sanadat_qapds', 'add_sanadat_sarfs', 'add_sell_bills'];
            }
            $user = User::where('id', $id)->first();
            
            if($user != null) {
                $user->syncPermissions($permissions);
                // $user->syncPermissions($request->permissions);
                
                DB::commit();
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error']);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error']);
        }
    }

}