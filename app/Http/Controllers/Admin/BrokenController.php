<?php

namespace App\Http\Controllers\Admin;

use Exception;

use App\Models\Quantity;
use App\Models\Broken;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SoldProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BrokenController extends Controller
{

    // auth
    public function __construct()
    {
        $this->middleware('auth');
    }

    // create
    public function index()
    {
        $page = config('app.page');
        $box = DB::select('SELECT remaining FROM box WHERE id = 10');
        $brokens = Broken::select('id', 'number', 'date_created', 'byan', 'customer_id','paid_balance')->with('customer:id,name')->orderBy('id', 'DESC')->paginate($page);
        $pages = ceil(Broken::count()/$page);

        return view('admin.broken.index', compact('brokens', 'pages', 'box'));
    }

    // create
    public function create()
    {
        $customers = DB::select('SELECT id, name FROM customers WHERE name LIKE "%تالفة%" ORDER BY id DESC');
        $products = DB::select('SELECT id, name, original_price, quantity FROM products ORDER BY id DESC');
        return view('admin.broken.create', compact('customers', 'products'));
    }

    // store
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $customer_id = $request['customer_id'];
            $paid_balance = abs($request['paid_balance']);

            $broken = new Broken;
            $broken->number = $request['number'];
            $broken->date_created = $request['date_created'];

            if ($request['target'] == 'customers') {
                $customer = Customer::where('id', $customer_id)->select('balance')->first();
                if ($customer != null) {
                    Customer::where('id', $customer_id)->update(['balance' => $customer->balance]);
                    $broken->customer_id = $customer_id;
                } else {
                    DB::rollBack();
                    throw new Exception('Customer not found');
                }
            }

            $broken->paid_balance = $paid_balance;
            $broken->total_balance =  $paid_balance;
            if ($request['byan'] == null) {
                $broken->byan = 'لا يوجد';
            } else {
                $broken->byan = $request['byan'];
            }
            $broken->save();

            $total_profit = 0;

            $tblArray = explode(',', $request['tbl']);
            for ($i = 0; $i < count($tblArray) / 5; $i++) {

                $product = Product::where('id', $tblArray[$i * 5 + 0])->first();
                $quantity = Quantity::where('id', $request->product_pr)->first();

                if ($quantity->quantity - $tblArray[$i * 5 + 1] < 0) {
                    return redirect('/brokens');

                } else if ($quantity->quantity - $tblArray[$i * 5 + 1] == 0) {

                    Product::where('id', $tblArray[$i * 5 + 0])->update([
                        'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                        'sell_bill_id' => $broken->id,
                        'status' => false
                    ]);
                }  else {
                    Product::where('id', $tblArray[$i * 5 + 0])->update([
                        'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                        'sell_bill_id' => $broken->id
                    ]);
                }
                Quantity::where('id', $request->product_pr)->update([
                    'quantity' => $quantity->quantity - $tblArray[$i * 5 + 1]
                ]);

                $sold_product = new SoldProduct;
                $sold_product->product_id = $tblArray[$i * 5 + 0];
                $sold_product->quantity = $tblArray[$i * 5 + 1];
                $sold_product->sell_price = $tblArray[$i * 5 + 2];
                $sold_product->total_price = $tblArray[$i * 5 + 3];
                $profit = ($tblArray[$i * 5 + 1] * $tblArray[$i * 5 + 2]) - ($tblArray[$i * 5 + 1] * $product->original_price);
                $sold_product->profit = $profit;
                $sold_product->buy_price = $quantity->buy_price;
                $sold_product->broken_id = $broken->id;
                $total_profit += $profit;
                $sold_product->save();
                
            }

            DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
                WHEN 10 THEN (SELECT remaining FROM box WHERE box.id = 10)+?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                ELSE box.counter
                END
            WHERE box.id IN(1, 9, 10);', [$paid_balance, $paid_balance, $paid_balance]);

            $date = date($request['date_created'] . ' H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,1,?,?)', [$paid_balance, 'فاتورة تالفة', $date]);

            DB::commit();
            return redirect('/broken/edit/' . $broken->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/broken/edit/' . $broken->id);
        }
    }

    // show
    public function show(Request $request)
    {

        $id = $request['id'];
        $bill = Broken::select('id', 'number', 'date_created', 'customer_id', 'total_balance', 'paid_balance', 'byan')->with('sold_product:id,product_id,quantity,sell_price,total_price,profit,sell_bill_id')->with('sold_product.product:id,name')->where('id', $id)->first();
        
        if ($bill != null) {
            $bill_data = view('admin.broken.show', compact('bill'))->render();
            return response()->json(['bill_data' => $bill_data]);
        } else {
            return response(['status' => 'error']);
        }
    }

    // edit
    public function edit(Request $request, $id)
    {

        $broken_bill = Broken::select('id', 'number', 'date_created', 'customer_id', 'total_balance', 'paid_balance', 'byan')->with('sold_product:id,product_id,quantity,sell_price,total_price,profit,sell_bill_id')->with('sold_product.product:id,name')->where('id', $id)->first();
        $customer = DB::select('SELECT id, name FROM customers WHERE name LIKE "%بضاعة تالفة%" ORDER BY id DESC');
        $products = DB::select('SELECT id, name, original_price, quantity FROM products ORDER BY id DESC');
        return view('admin.broken.edit', compact('broken_bill', 'customer', 'products'));

    }

    // update
    public function update (Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $customer_id = $request['customer_id'];
            $paid_balance = abs($request['paid_balance']);

            $broken = Broken::where('id', $id)->first();

            if ($request['target'] == 'customers') {
                $customer = Customer::where('id', $customer_id)->select('balance')->first();
                if ($customer != null) {
                    Customer::where('id', $customer_id)->update(['balance' => $customer->balance]);
                    $broken->customer_id = $customer_id;
                } else {
                    DB::rollBack();
                    throw new Exception('Customer not found');
                }
            }

            $total_profit = 0;

            $tblArray = explode(',', $request['tbl']);
            for ($i = 0; $i < count($tblArray) / 5; $i++) {

                $product = Product::where('id', $tblArray[$i * 5 + 0])->first();
                $quantity = Quantity::where('id', $request->product_pr)->first();

                if ($quantity->quantity - $tblArray[$i * 5 + 1] < 0) {
                    return redirect('/brokens');

                } else if ($quantity->quantity - $tblArray[$i * 5 + 1] == 0) {

                    Product::where('id', $tblArray[$i * 5 + 0])->update([
                        'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                        'sell_bill_id' => $broken->id,
                        'status' => false
                    ]);
                }  else {
                    Product::where('id', $tblArray[$i * 5 + 0])->update([
                        'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                        'sell_bill_id' => $broken->id
                    ]);
                }
                Quantity::where('id', $request->product_pr)->update([
                    'quantity' => $quantity->quantity - $tblArray[$i * 5 + 1]
                ]);

                $sold_product = new SoldProduct;
                $sold_product->product_id = $tblArray[$i * 5 + 0];
                $sold_product->quantity = $tblArray[$i * 5 + 1];
                $sold_product->sell_price = $tblArray[$i * 5 + 2];
                $sold_product->total_price = $tblArray[$i * 5 + 3];
                $profit = ($tblArray[$i * 5 + 1] * $tblArray[$i * 5 + 2]) - ($tblArray[$i * 5 + 1] * $product->original_price);
                $sold_product->profit = $profit;
                $sold_product->buy_price = $quantity->buy_price;
                $sold_product->broken_id = $broken->id;
                $total_profit += $profit;
                $sold_product->save();
                
            }

            DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
                WHEN 10 THEN (SELECT remaining FROM box WHERE box.id = 10)+?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                ELSE box.counter
                END
            WHERE box.id IN(1, 9, 10);', [$paid_balance - $broken->paid_balance, $paid_balance - $broken->paid_balance]);

            $broken->paid_balance = $paid_balance;
            $broken->total_balance =  $paid_balance;
            if ($request['byan'] == null) {
                $broken->byan = 'لا يوجد';
            } else {
                $broken->byan = $request['byan'];
            }
            $broken->save();

            $date = date('Y-m-d H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,1,?,?)', [$paid_balance, 'فاتورة تالفة', $date]);

            DB::commit();
            return redirect('/broken/edit/' . $id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/broken/edit/' . $id);
        }

    }

}
