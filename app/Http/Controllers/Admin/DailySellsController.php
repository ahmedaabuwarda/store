<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\SellBill;
use App\Models\SoldProduct;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailySellsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $page = config('app.page');
        $sell_bills = SellBill::select('id', 'number', 'date_created', 'byan', 'provider_id', 'customer_id', 'worker_id', 'remaining_balance', 'paid_balance', 'total_profit')->where('customer_id', 1)->with('user:id,name')->with('customer:id,name')->with('provider:id,name')->orderBy('id', 'DESC')->paginate($page);

        $pages = ceil(SellBill::count() / $page);
        $box = DB::select('SELECT remaining from box where id IN (3,7);');
        if ($request->ajax()) {
            $table = view('admin.sell_bill.table', compact('sell_bills'))->render();
            return response()->json(['status' => 'success' ,'table' => $table]);
        } else {
            return view('admin.daily_sells.index', compact('sell_bills', 'pages', 'box'));
        }

    }

    public function create()
    {
        $customers = DB::select('SELECT id, name FROM customers WHERE id = 1 ORDER BY id DESC');
        $products = DB::select('SELECT id, name, original_price, quantity FROM products WHERE original_price != 0 ORDER BY id DESC');
        $modal = view('admin.daily_sells.create', compact('customers', 'products'))->render();
        return response()->json(['status' => 'success', 'modal' => $modal]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $customer_id = $request['customer_id'];
            $worker_id = $request['worker_id'];
            $provider_id = $request['provider_id'];
            $remaining_balance = $request['remaining_balance'];
            $paid_balance = abs($request['paid_balance']);

            $sell_bill = new SellBill;
            $sell_bill->number = $request['number'];
            $sell_bill->date_created = $request['date_created'];
            if ($request['target'] == 'customers') {
                $customer = Customer::where('id', $customer_id)->select('balance')->first();
                if ($customer != null) {
                    Customer::where('id', $customer_id)->update(['balance' => $customer->balance + $remaining_balance]);
                    $sell_bill->customer_id = $customer_id;
                } else {
                    DB::rollBack();
                    throw new Exception('Customer not found');
                }
            } elseif ($request['target'] == 'providers') {
                $provider = Provider::where('id', $provider_id)->select('balance')->first();
                if ($provider != null) {
                    Provider::where('id', $provider_id)->update(['balance' => $provider->balance + $remaining_balance]);
                    $sell_bill->provider_id = $provider_id;
                } else {
                    DB::rollBack();
                    throw new Exception('Provider not found');
                }
            } elseif ($request['target'] == 'workers') {
                $worker = User::where('id', $worker_id)->select('balance')->first();
                if ($worker != null) {
                    User::where('id', $worker_id)->update(['balance' => $worker->balance + $remaining_balance]);
                    $sell_bill->worker_id = $worker_id;
                } else {
                    DB::rollBack();
                    throw new Exception('Worker not found');
                }
            }

            $sell_bill->paid_balance = $paid_balance;
            $sell_bill->remaining_balance = 0;
            $sell_bill->discount = 0;
            $sell_bill->total_balance = abs($remaining_balance) + $paid_balance;
            if ($request['byan'] == null) {
                $sell_bill->byan = 'لا يوجد';
            } else {
                $sell_bill->byan = $request['byan'];
            }
            $sell_bill->total_profit = 0;
            $sell_bill->save();

            $total_profit = 0;

            $tblArray = explode(',', $request['tbl']);
            for ($i = 0; $i < count($tblArray) / 5; $i++) {
                $product = Product::where('id', $tblArray[$i * 5 + 0])->first();
                Product::where('id', $tblArray[$i * 5 + 0])->update([
                    'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                    'sell_bill_id' => $sell_bill->id
                ]);
                if ($product->quantity == 0) {
                    Product::where('id', $tblArray[$i * 5 + 0])->update(['status' => false]);
                }
                $sold_product = new SoldProduct;
                $sold_product->product_id = $tblArray[$i * 5 + 0];
                $sold_product->quantity = $tblArray[$i * 5 + 1];
                $sold_product->sell_price = $tblArray[$i * 5 + 2];
                $sold_product->total_price = $tblArray[$i * 5 + 3];
                $sold_product->sell_bill_id = $sell_bill->id;
                $sold_product->profit = $tblArray[$i * 5 + 4];
                $total_profit += $tblArray[$i * 5 + 4];
                $sold_product->save();
            }

            SellBill::where('id', $sell_bill->id)->update(['total_profit' => $total_profit]);

            DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)+?
                WHEN 3 THEN (SELECT remaining FROM box WHERE box.id = 3)+?
                WHEN 7 THEN (SELECT remaining FROM box WHERE box.id = 7)+?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                WHEN 7 THEN (SELECT counter FROM box WHERE box.id = 7)+1
                ELSE box.counter
                END
            WHERE box.id IN(1, 3, 7);', [$paid_balance, $total_profit, $sell_bill->total_balance]);

            $date = date($request['date_created'] . ' H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,1,?,?)', [$paid_balance, 'فاتورة بيع', $date]);

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show(Request $request)
    {
        $id = $request['id'];
        $bill = SellBill::select('id', 'number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'total_balance', 'paid_balance', 'remaining_balance', 'discount', 'byan', 'total_profit')->with('sold_product:id,product_id,quantity,sell_price,total_price,profit,sell_bill_id')->with('sold_product.product:id,name')->where('id', $id)->first();
        if ($bill != null) {
            $bill_data = view('includes.bill_data', compact('bill'))->render();
            return response()->json(['bill_data' => $bill_data]);
        } else {
            return response(['status' => 'error']);
        }
    }

    public function edit($id)
    {
        $sell_bill = SellBill::select('id', 'number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'total_balance', 'total_profit', 'paid_balance', 'remaining_balance', 'discount', 'byan')->with('sold_product:id,product_id,quantity,sell_price,total_price,profit,sell_bill_id')->where('id', $id)->first();
        $products = DB::select('SELECT id, name, original_price, quantity FROM products ORDER BY id DESC');
        return view('admin.daily_sells.edit', compact('sell_bill', 'products'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            if ($request['tbl'] == null) {
                return redirect('/daily_sells');
            } else {

                $total_profit = 0;
                $tblArray = explode(',', $request['tbl']);
                for ($i = 0; $i < count($tblArray) / 5; $i++) {
                    $product = Product::where('id', $tblArray[$i * 5 + 0])->first();
                    if ($product->quantity - $tblArray[$i * 5 + 1] == 0) {
                        Product::where('id', $tblArray[$i * 5 + 0])->update([
                            'status' => false,
                            'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                            'sell_bill_id' => $id
                        ]);
                    } else if ($product->quantity - $tblArray[$i * 5 + 1] > 0){
                        Product::where('id', $tblArray[$i * 5 + 0])->update([
                            'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                            'sell_bill_id' => $id
                        ]);
                    }

                    $sold_product = new SoldProduct;
                    $sold_product->product_id = $tblArray[$i * 5 + 0];
                    $sold_product->quantity = $tblArray[$i * 5 + 1];
                    $sold_product->sell_price = $tblArray[$i * 5 + 2];
                    $sold_product->total_price = $tblArray[$i * 5 + 3];
                    $sold_product->profit = $tblArray[$i * 5 + 4];
                    $sold_product->sell_bill_id = $id;
                    $total_profit += $tblArray[$i * 5 + 4];
                    $sold_product->save();

                }
            
                $sell_bill = SellBill::where('id', $id)->first();

                DB::statement('UPDATE box SET box.remaining = CASE box.id
                    WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)+?
                    WHEN 3 THEN (SELECT remaining FROM box WHERE box.id = 3)+?
                    WHEN 7 THEN (SELECT remaining FROM box WHERE box.id = 7)+?
                    ELSE box.remaining
                    END,
                box.counter = CASE box.id
                    WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                    ELSE box.counter
                    END
                WHERE box.id IN(1, 3, 7);', [$request['paid_balance'] - $sell_bill->paid_balance, $total_profit, (abs($request['remaining_balance']) + abs($request['paid_balance'])) - $sell_bill->total_balance]);

                SellBill::where('id', $id)->update(
                    [
                        'paid_balance' => $request['paid_balance'],
                        'remaining_balance' => $request['remaining_balance'],
                        'total_balance' => abs($request['remaining_balance']) + abs($request['paid_balance']),
                        'total_profit' => $sell_bill->total_profit + $total_profit,
                        'byan' => $request['byan']
                    ]
                );

                if ($request['provider_id'] > 0) {
                    $provider = Provider::where('id', $request['provider_id'])->select('balance')->first();
                    Provider::where('id', $request['provider_id'])->update(['balance' => ($provider->balance - $sell_bill->remaining_balance) +  $request['remaining_balance']]);
                } elseif ($request['customer_id'] > 0) {
                    $customer = Customer::where('id', $request['customer_id'])->select('balance')->first();
                    Customer::where('id', $request['customer_id'])->update(['balance' => ($customer->balance - $sell_bill->remaining_balance) +  $request['remaining_balance']]);
                } elseif ($request['worker_id'] > 0) {
                    $worker = User::where('id', $request['worker_id'])->select('balance')->first();
                    User::where('id', $request['worker_id'])->update(['balance' => ($worker->balance - $sell_bill->remaining_balance) +  $request['remaining_balance']]);
                }
            }

            DB::commit();
            return redirect('/daily_sells');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/daily_sells')->with('error', 'Error: ' . $e->getMessage());
        }
    }

}