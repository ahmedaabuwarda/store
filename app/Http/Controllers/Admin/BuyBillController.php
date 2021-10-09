<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;
// use App\Models\Worker;
use App\Models\User;
use App\Models\BuyBill;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\BuyedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BuyBillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $page = config('app.page');
        $buy_bills = BuyBill::select('id', 'number', 'date_created', 'byan', 'provider_id', 'customer_id', 'worker_id', 'remaining_balance','paid_balance')->with('worker:id,name')->with('customer:id,name')->with('provider:id,name')->orderBy('id', 'DESC')->paginate($page);
        $pages = ceil(BuyBill::count()/$page);
        return view('admin.buy_bill.index', compact('buy_bills', 'pages'));
    }
    public function create()
    {
        $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
        $customers = DB::select('SELECT id, name FROM customers ORDER BY id DESC');
        $workers = DB::select('SELECT id, name FROM users ORDER BY id DESC');
        $products = DB::select('SELECT id, name, original_price, quantity FROM products ORDER BY id DESC');
        return view('admin.buy_bill.create', compact('providers', 'customers', 'workers', 'products'));
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

            $buy_bill = new BuyBill;
            $buy_bill->number = $request['number'];
            $buy_bill->date_created = $request['date_created'];

            if ($request['target'] == 'customers'){
                $customer = Customer::where('id', $customer_id)->select('balance')->first();
                if($customer != null){
                    Customer::where('id', $customer_id)->update(['balance' => $customer->balance + $remaining_balance]);
                    $buy_bill->customer_id = $customer_id;
                } else {
                    throw new Exception('Customer not found');
                }
            } elseif($request['target'] == 'providers') {
                $provider = Provider::where('id', $provider_id)->select('balance')->first();
                if($provider != null){
                    Provider::where('id', $provider_id)->update(['balance' => $provider->balance + $remaining_balance]);
                    $buy_bill->provider_id = $provider_id;
                } else {
                    throw new Exception('Provider not found');
                }
            } elseif($request['target'] == 'workers') {
                $worker = User::where('id', $worker_id)->select('balance')->first();
                if($worker != null){
                    User::where('id', $worker_id)->update(['balance' => $worker->balance + $remaining_balance]);
                    $buy_bill->worker_id = $worker_id;
                } else {
                    throw new Exception('Worker not found');
                }
            }
            $buy_bill->paid_balance = $paid_balance;
            $buy_bill->remaining_balance = $remaining_balance;
            $buy_bill->discount = $request['discount'];
            $buy_bill->original_balance = abs($remaining_balance) + $paid_balance;
            if($request['byan'] == null){
                $buy_bill->byan = 'لا يوجد';
            } else {
                $buy_bill->byan = $request['byan'];
            }
            $buy_bill->save();

            $tblArray = explode(',', $request['tbl']);
            for($i = 0; $i < count($tblArray)/4; $i++) {
                $product = Product::where('id', $tblArray[$i*4 + 0])->first();
                Product::where('id', $tblArray[$i*4 + 0])->update([
                    'quantity' => $product->quantity + $tblArray[$i*4 + 1],
                    'original_quantity' => $product->original_quantity + $tblArray[$i*4 + 1],
                    'original_price' => $tblArray[$i*4 + 2],
                    'buy_bill_id' => $buy_bill->id,
                    'status' => true
                ]);
                $buyed_product = new BuyedProduct;
                $buyed_product->product_id = $tblArray[$i*4 + 0];
                $buyed_product->quantity = $tblArray[$i*4 + 1];
                $buyed_product->buy_price = $tblArray[$i*4 + 2];
                $buyed_product->total_price = $tblArray[$i*4 + 3];
                $buyed_product->buy_bill_id = $buy_bill->id;
                $buyed_product->save();
            }

            DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
                WHEN 6 THEN (SELECT remaining FROM box WHERE box.id = 6)+?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                WHEN 6 THEN (SELECT counter FROM box WHERE box.id = 6)+1
                ELSE box.counter
                END
            WHERE box.id IN(1, 6);', [$paid_balance, $paid_balance + abs($remaining_balance)]);

            $date = date($request['date_created'].' H:i:s');
            DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created) VALUES (?,0,?,?)',[$paid_balance,'فاتورة شراء', $date]);

            DB::commit();
            return redirect('/buy_bills');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/buy_bills')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function show(Request $request)
    {
        $id = $request['id'];
        $bill = BuyBill::select('id','number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'original_balance', 'paid_balance', 'remaining_balance', 'discount', 'byan')->with('buyed_product:id,product_id,quantity,buy_price,total_price,buy_bill_id')->with('buyed_product.product:id,name')->where('id', $id)->first();
        if($bill != null) {
            $bill_data = view('includes.bill_data', compact('bill'))->render();
            return response()->json(['bill_data' => $bill_data]);
        } else {
            return response(['status' => 'error']);
        }
    }
    public function edit($id)
    {
        $buy_bill = BuyBill::select('id','number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'original_balance', 'paid_balance', 'remaining_balance', 'discount', 'byan')->with('buyed_product:id,product_id,quantity,buy_price,total_price,buy_bill_id')->where('id', $id)->first();
        $products = DB::select('SELECT id, name, original_price, quantity FROM products ORDER BY id DESC');
        return view('admin.buy_bill.edit', compact('buy_bill', 'products'));
    }
    public function update(Request $request, $id)
    {
        if($request['tbl'] == null) {
            return redirect('/buy_bills');
        } else {
            DB::beginTransaction();
            try {
                $tblArray = explode(',', $request['tbl']);
                for($i = 0; $i < count($tblArray)/4; $i++) {
                    $product = Product::where('id', $tblArray[$i*4 + 0])->first();
                    Product::where('id', $tblArray[$i*4 + 0])->update([
                        'quantity' => $product->quantity + $tblArray[$i*4 + 1],
                        'original_quantity' => $product->original_quantity + $tblArray[$i*4 + 1],
                        'original_price' => $tblArray[$i*4 + 2],
                        'buy_bill_id' => $id
                    ]);
                    $buyed_product = new BuyedProduct;
                    $buyed_product->product_id = $tblArray[$i*4 + 0];
                    $buyed_product->quantity = $tblArray[$i*4 + 1];
                    $buyed_product->buy_price = $tblArray[$i*4 + 2];
                    $buyed_product->total_price = $tblArray[$i*4 + 3];
                    $buyed_product->buy_bill_id = $id;
                    $buyed_product->save();
                }
                $buy_bill = BuyBill::where('id', $id)->first();
                BuyBill::where('id', $id)->update([
                    'paid_balance' => $request['paid_balance'],
                    'remaining_balance' => $request['remaining_balance'],
                    'original_balance' => $request['remaining_balance'] + $request['paid_balance'],
                    'byan' => $request['byan']]
                );

                if($request['provider_id'] > 0){
                    $provider = Provider::where('id', $request['provider_id'])->select('balance')->first();
                    if($provider != null){
                        Provider::where('id', $request['provider_id'])->update(['balance' => ($provider->balance - $buy_bill->remaining_balance) +  $request['remaining_balance']]);
                    } else {
                        throw new \Exception('Provider not found');
                    }
                } elseif($request['customer_id'] > 0){
                    $customer = Customer::where('id', $request['customer_id'])->select('balance')->first();
                    if($customer != null) {
                        Customer::where('id', $request['customer_id'])->update(['balance' => ($customer->balance - $buy_bill->remaining_balance) +  $request['remaining_balance']]);
                    } else {
                        throw new \Exception('Customer not found');
                    }
                } elseif($request['worker_id'] > 0){
                    $worker = User::where('id', $request['worker_id'])->select('balance')->first();
                    if($worker != null) {
                        User::where('id', $request['worker_id'])->update(['balance' => ($worker->balance - $buy_bill->remaining_balance) +  $request['remaining_balance']]);
                    } else {
                        throw new \Exception('Worker not found');
                    }
                }

                DB::commit();
                return redirect('/buy_bills');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect('/buy_bills')->with('error', 'Error: ' . $e->getMessage());
            }
        }
    }
    public function delete_product($id)
    {
        DB::beginTransaction();
        try {
            $buyed_product = BuyedProduct::where('id', $id)->with('buy_bill')->first();

            BuyBill::where('id', $buyed_product->buy_bill_id)->update([
                'original_balance' => $buyed_product->buy_bill->original_balance - $buyed_product->total_price
            ]);

            $product = Product::where('id', $buyed_product->product_id)->first();

            Product::where('id', $buyed_product->product_id)->update([
                'quantity' => $product->quantity - $buyed_product->quantity,
                'original_quantity' => $product->original_quantity - $buyed_product->quantity
            ]);

            $buyed_product->delete();

            DB::commit();
            return redirect('/buy_bill/edit/' . $buyed_product->buy_bill_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/buy_bills')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function to_pdf(Request $request)
    {
        $from = $request['from'];
        $to = $request['to'];
        $buy_bills = BuyBill::select('id', 'number', 'date_created', 'byan','provider_id', 'customer_id', 'worker_id', 'remaining_balance','paid_balance')->with('worker:id,name')->with('customer:id,name')->with('provider:id,name')->whereRaw('date_created >= ? AND date_created <= ?',[$from, $to])->orderBy('id', 'DESC')->get();

        $i = 1; $total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">محلات النور - ابووردة لقطع غيار الدراجات النارية</h3><h1 align="center">كشف كل فواتير الشراء</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%">#</th>
            <th width="20%">رقم الفاتورة</th>
            <th width="15%">تاريخ الانشاء</th>
            <th width="20%">المستهلك</th>
            <th width="15%">المبلغ المدفوع</th>
            <th width="10%">المبلغ المتبقي</th>
            <th width="15%">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach($buy_bills as $buy_bill) {
            $target = ''; $balance = '';
            if($buy_bill->provider_id > 0){
                $target = $buy_bill->provider->name.' - مورد';
            } elseif($buy_bill->customer_id > 0){
                $target = $buy_bill->customer->name.' - زبون';
            } elseif($buy_bill->worker_id > 0){
                $target = $buy_bill->worker->name.' - موظف';
            }
            if($buy_bill->remaining_balance > 0) {
                $balance = $buy_bill->remaining_balance.'<span>&#8362;&#160;</span> - دائن -';
            } elseif($buy_bill->remaining_balance < 0) {
                $balance = $buy_bill->remaining_balance.'<span>&#8362;&#160;</span> - مدين -';
            } else {
                $balance = $buy_bill->remaining_balance.'<span>&#8362;&#160;</span>';
            }
            $table_content .= '<tr>
              <td width="5%">'.$i.'</td>
              <td width="20%">'.$buy_bill->number.'</td>
              <td width="15%">'.$buy_bill->date_created.'</td>
              <td width="20%">'.$target.'</td>
              <td width="15%">'.$buy_bill->paid_balance.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$balance.'</td>
              <td width="15%">'.$buy_bill->byan.'</td>
            </tr>';
            $total += $buy_bill->remaining_balance; $i++;
        }
        if($total < 0) {
            $total = $total.'<span>&#8362;&#160;</span> - مدين -';
        } elseif($total > 0) {
            $total = $total.'<span>&#8362;&#160;</span> - دائن -';
        } else {
            $total = $total.'<span>&#8362;&#160;</span>';
        }
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل فواتير الشراء');
        PDF::SetAuthor('اياد الهسي');
        // set some language dependent data:
        $lg = Array();
        $lg['a_meta_charset'] = 'UTF-8';
        $lg['a_meta_dir'] = 'rtl';
        $lg['a_meta_language'] = 'ar';
        $lg['w_page'] = 'page';
        // set some language-dependent strings (optional)
        PDF::setLanguageArray($lg);
        // set font
        PDF::SetFont('aealarabiya', '', 11);
        // set margins
        PDF::SetMargins(PDF_MARGIN_LEFT, /*PDF_MARGIN_TOP,*/ PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

        PDF::AddPage();
        PDF::writeHTML($content);
        PDF::SetFont('freeserif', '', 11);
        PDF::writeHTML($table_content);

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">'.$total.'</td></tr></tbody></table>');
        PDF::Output('all_buy_bills_'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
