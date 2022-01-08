<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\User;
use App\Models\Worker;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\SellBill;
use App\Models\SoldProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            $table = view('admin.daily_sells.table', compact('sell_bills'))->render();
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
                $worker = Worker::where('id', $worker_id)->select('balance')->first();
                if ($worker != null) {
                    Worker::where('id', $worker_id)->update(['balance' => $worker->balance + $remaining_balance]);
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
                $profit = ($tblArray[$i * 5 + 1] * $tblArray[$i * 5 + 2]) - ($tblArray[$i * 5 + 1] * $product->original_price);
                $sold_product->profit = $profit;
                $sold_product->sell_bill_id = $sell_bill->id;
                $total_profit += $profit;
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
        } catch (Exception $e) {
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

        $paid_balance = abs($request->paid_balance);
        $remaining_balance = $request->remaining_balance;

        $sell_bill = SellBill::where('id', $id)->first();

        DB::beginTransaction();
        try {
            if ($request['tbl'] == null && $sell_bill->paid_balance != $paid_balance) {
                return redirect('/daily_sells');
            } else {

                $total_profit = 0;
                $tblArray = explode(',', $request['tbl']);

                if ($request->tbl != null) {
                    for ($i = 0; $i < count($tblArray) / 5; $i++) {
                        $product = Product::where('id', $tblArray[$i * 5 + 0])->first();
                        if ($product->quantity - $tblArray[$i * 5 + 1] == 0) {
                            Product::where('id', $tblArray[$i * 5 + 0])->update([
                                'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                                'sell_bill_id' => $id,
                                'status' => false
                            ]);
                        } else if ($product->quantity - $tblArray[$i * 5 + 1] > 0){
                            Product::where('id', $tblArray[$i * 5 + 0])->update([
                                'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                                'sell_bill_id' => $id,
                                'status' => true
                            ]);
                        }

                        $sold_product = new SoldProduct;
                        $sold_product->product_id = $tblArray[$i * 5 + 0];
                        $sold_product->quantity = $tblArray[$i * 5 + 1];
                        $sold_product->sell_price = $tblArray[$i * 5 + 2];
                        $sold_product->total_price = $tblArray[$i * 5 + 3];
                        $profit = ($tblArray[$i * 5 + 1] * $tblArray[$i * 5 + 2]) - ($tblArray[$i * 5 + 1] * $product->original_price);
                        $sold_product->profit = $profit;
                        $sold_product->sell_bill_id = $id;
                        $total_profit += $profit;
                        $sold_product->save();

                    }
                }

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
                WHERE box.id IN(1, 3, 7);', [($paid_balance - $sell_bill->paid_balance), $total_profit, ((abs($remaining_balance) + abs($paid_balance)) - $sell_bill->total_balance)]);

                SellBill::where('id', $id)->update([
                        'paid_balance' => $paid_balance,
                        'remaining_balance' => $remaining_balance,
                        'total_balance' => abs($remaining_balance) + $paid_balance,
                        'total_profit' => $sell_bill->total_profit + $total_profit,
                        'byan' => $request['byan']
                    ]);

                if ($request['provider_id'] > 0) {
                    $provider = Provider::where('id', $request['provider_id'])->select('balance')->first();
                    Provider::where('id', $request['provider_id'])->update(['balance' => ($provider->balance - $sell_bill->remaining_balance) +  $request['remaining_balance']]);
                } elseif ($request['customer_id'] > 0) {
                    $customer = Customer::where('id', $request['customer_id'])->select('balance')->first();
                    Customer::where('id', $request['customer_id'])->update(['balance' => ($customer->balance - $sell_bill->remaining_balance) +  $request['remaining_balance']]);
                } elseif ($request['worker_id'] > 0) {
                    $worker = Worker::where('id', $request['worker_id'])->select('balance')->first();
                    Worker::where('id', $request['worker_id'])->update(['balance' => ($worker->balance - $sell_bill->remaining_balance) +  $request['remaining_balance']]);
                }
            }

            DB::commit();
            return redirect('/daily_sell/edit/'.$id);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/daily_sells')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete_product($id)
    {
        DB::beginTransaction();
        try {
            $sold_product = SoldProduct::where('id', $id)->with('sell_bill')->first();
            $product = Product::where('id', $sold_product->product_id)->first();

            $profit = ($sold_product->sell_price * $sold_product->quantity) - ($product->original_price * $sold_product->quantity);

            DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
                WHEN 3 THEN (SELECT remaining FROM box WHERE box.id = 3)-?
                WHEN 7 THEN (SELECT remaining FROM box WHERE box.id = 7)-?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                ELSE box.counter
                END
            WHERE box.id IN(1, 3, 7);', [$sold_product->total_price, $profit, $sold_product->total_price]);

            $sell_bill = SellBill::where('id', $sold_product->sell_bill_id)->update([
                'total_balance' => $sold_product->sell_bill->total_balance - $sold_product->total_price,
                'paid_balance' => $sold_product->sell_bill->paid_balance - $sold_product->total_price,
                'total_profit' => $sold_product->sell_bill->total_profit - $profit
            ]);
  
            Product::where('id', $sold_product->product_id)->update([
                'quantity' => $product->quantity + $sold_product->quantity,
                'status' => true
            ]);

            $sold_product->delete();

            DB::commit();
            return redirect('/daily_sell/edit/' . $sold_product->sell_bill_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/daily_sells')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function to_pdf(Request $request)
    {
        $from = $request['from'];
        $to = $request['to'];
        $sell_bills = SellBill::select('id', 'number', 'date_created', 'byan', 'provider_id', 'customer_id', 'worker_id', 'remaining_balance', 'paid_balance', 'total_profit')->with('worker:id,name')->with('customer:id,name')->with('provider:id,name')->whereRaw('date_created >= ? AND date_created <= ? AND customer_id = 1', [$from, $to])->orderBy('id', 'DESC')->get();

        $i = 1;
        $total_rem = 0;
        $total_profit = 0;
        $total_paid = 0;
        $time = date('H:i:s');
        $date = date('Y-m-d');
        $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف كل فواتير البيع</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">#</th>
            <th width="15%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">المستهلك</th>
            <th width="10%" bgcolor="#eee">المبلغ المدفوع</th>
            <th width="10%" bgcolor="#eee">المبلغ المتبقي</th>
            <th width="10%" bgcolor="#eee">المربح</th>
            <th width="15%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
        foreach ($sell_bills as $sell_bill) {
            $target = '';
            $balance = '';
            if ($sell_bill->provider_id > 0) {
                $target = $sell_bill->provider->name . ' - مورد';
            } elseif ($sell_bill->customer_id > 0) {
                $target = $sell_bill->customer->name . ' - زبون';
            } elseif ($sell_bill->worker_id > 0) {
                $target = $sell_bill->worker->name . ' - موظف';
            }
            if ($sell_bill->remaining_balance > 0) {
                $balance = $sell_bill->remaining_balance . '<span>&#8362;&#160;</span> - دائن -';
            } elseif ($sell_bill->remaining_balance < 0) {
                $balance = $sell_bill->remaining_balance . '<span>&#8362;&#160;</span> - مدين -';
            } else {
                $balance = $sell_bill->remaining_balance . '<span>&#8362;&#160;</span>';
            }
            $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="15%">' . $sell_bill->number . '</td>
              <td width="15%">' . $sell_bill->date_created . '</td>
              <td width="20%">' . $target . '</td>
              <td width="10%">' . $sell_bill->paid_balance . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $balance . '</td>
              <td width="10%">' . $sell_bill->total_profit . '<span>&#8362;&#160;</span></td>
              <td width="15%">' . $sell_bill->byan . '</td>
            </tr>';
            $total_rem += $sell_bill->remaining_balance;
            $total_profit += $sell_bill->total_profit;
            $total_paid += $sell_bill->paid_balance;
            $i++;
        }
        if ($total_rem < 0) {
            $total_rem = $total_rem . '<span>&#8362;&#160;</span> - مدين -';
        } elseif ($total_rem > 0) {
            $total_rem = $total_rem . '<span>&#8362;&#160;</span> - دائن -';
        } else {
            $total_rem = $total_rem . '<span>&#8362;&#160;</span>';
        }
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل فواتير البيع');
        PDF::SetAuthor('اياد الهسي');
        // set some language dependent data:
        $lg = array();
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center">
        <tbody>
        <tr>
            <td width="10%">#</td>
            <td width="60%">المجموع</td>
        </tr>
        <tr>
            <td width="10%">#</td>
            <td width="30%">المبلغ المتبقي</td>
            <td width="30%" color="#fff" bgcolor="#003B36">' . $total_rem . '</td>
        </tr>
        <tr>
            <td width="10%">#</td>
            <td width="30%">المبلغ المدفوع</td>
            <td width="30%" color="#fff" bgcolor="#DB2E39">' . $total_paid . '<span>&#8362;&#160;</span></td>
        </tr>
        <tr>
            <td width="10%">#</td>
            <td width="30%">المربح</td>
            <td width="30%" color="#000" bgcolor="#FFCA2C">' . $total_profit . '<span>&#8362;&#160;</span></td>
        </tr>
        </tbody></table>');

        PDF::Output('all_sell_bills_' . date('ymdhis') . '.pdf', 'I');
        return response()->json(['status' => 'success']);
    }

}