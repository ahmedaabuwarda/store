<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Quantity;
use App\Models\Worker;
use App\Models\Box;
use App\Models\BuyBill;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\BuyedProduct;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
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
    $box = DB::select('SELECT remaining FROM box WHERE id = 6');
    $buy_bills = BuyBill::select('id', 'number', 'date_created', 'byan', 'provider_id', 'customer_id', 'worker_id', 'remaining_balance', 'paid_balance')->with('user:id,name')->with('customer:id,name')->with('provider:id,name')->orderBy('id', 'DESC')->paginate($page);
    $pages = ceil(BuyBill::count() / $page);

    return view('admin.buy_bill.index', compact('buy_bills', 'pages', 'box'));
  }

  public function create()
  {
    $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
    $customers = DB::select('SELECT id, name FROM customers ORDER BY id DESC');
    $workers = DB::select('SELECT id, name FROM workers ORDER BY id DESC');
    $products = DB::select('SELECT id, name, original_price, quantity FROM products ORDER BY id DESC');
    $boxes = Box::select('id', 'name')->get();

    return view('admin.buy_bill.create', compact('providers', 'customers', 'workers', 'products', 'boxes'));
  }

  public function store(Request $request)
  {
    $user_id = Auth::user()->id;
    $box_id = $request['box_id'];
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

      if ($request['target'] == 'customers') {
        $customer = Customer::where('id', $customer_id)->select('balance')->first();
        if ($customer != null) {
          Customer::where('id', $customer_id)->update(['balance' => $customer->balance + $remaining_balance]);
          $buy_bill->customer_id = $customer_id;
        } else {
          throw new Exception('Customer not found');
        }
      } elseif ($request['target'] == 'providers') {
        $provider = Provider::where('id', $provider_id)->select('balance')->first();
        if ($provider != null) {
          Provider::where('id', $provider_id)->update(['balance' => $provider->balance + $remaining_balance]);
          $buy_bill->provider_id = $provider_id;
        } else {
          throw new Exception('Provider not found');
        }
      } elseif ($request['target'] == 'workers') {
        $worker = Worker::where('id', $worker_id)->select('balance')->first();
        if ($worker != null) {
          Worker::where('id', $worker_id)->update(['balance' => $worker->balance + $remaining_balance]);
          $buy_bill->worker_id = $worker_id;
        } else {
          throw new Exception('Worker not found');
        }
      }

      $buy_bill->paid_balance = $paid_balance;
      $buy_bill->remaining_balance = $remaining_balance;
      $buy_bill->expense = $request['expense'];
      $buy_bill->original_balance = abs($remaining_balance) + $paid_balance;

      $buy_bill->byan = $request['byan'] ?? 'لا يوجد';

      $buy_bill->save();

      $tblArray = explode(',', $request['tbl']);
      // dd($request);
      if ($tblArray == null) {
        DB::rollBack();
        return redirect('/buy_bills');
      }

      for ($i = 0; $i < count($tblArray) / 4; $i++) {

        $buyed_product = new BuyedProduct;
        $buyed_product->product_id = $tblArray[$i * 4 + 0];
        $buyed_product->quantity = $tblArray[$i * 4 + 1];
        $buyed_product->buy_price = $tblArray[$i * 4 + 2];
        $buyed_product->total_price = $tblArray[$i * 4 + 3];
        $buyed_product->buy_bill_id = $buy_bill->id;
        $buyed_product->save();

        $quantity = Quantity::where('product_id', $tblArray[$i * 4 + 0])->where('buy_price', $tblArray[$i * 4 + 2])->first();
        $product = Product::where('id', $tblArray[$i * 4 + 0])->first();

        if ($quantity == null) {

          $quantit = new Quantity;
          $quantit->product_id = $tblArray[$i * 4 + 0];
          $quantit->quantity = $tblArray[$i * 4 + 1];
          $quantit->buy_price = $tblArray[$i * 4 + 2];
          $quantit->save();
        } else if ($quantity != null) {

          Quantity::where('product_id', $tblArray[$i * 4 + 0])->where('buy_price', $tblArray[$i * 4 + 2])->update([
            'quantity' => $quantity->quantity + $tblArray[$i * 4 + 1],
          ]);
        }

        Product::where('id', $tblArray[$i * 4 + 0])->update([
          'quantity' => $product->quantity + $tblArray[$i * 4 + 1],
          'original_quantity' => $product->original_quantity + $tblArray[$i * 4 + 1],
          'original_price' => $tblArray[$i * 4 + 2],
          'buy_bill_id' => $buy_bill->id,
          'status' => true
        ]);
      }

      // DB::statement('UPDATE box SET box.remaining = CASE box.id
      //           WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)-?
      //           WHEN 6 THEN (SELECT remaining FROM box WHERE box.id = 6)+?
      //           ELSE box.remaining
      //           END,
      //       box.counter = CASE box.id
      //           WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
      //           WHEN 6 THEN (SELECT counter FROM box WHERE box.id = 6)+1
      //           ELSE box.counter
      //           END
      //       WHERE box.id IN(1, 6);', [$paid_balance, $paid_balance + abs($remaining_balance)]);

      $date = date($request['date_created'] . ' H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,1,?,?,?,?)', [$paid_balance, 'فاتورة عينيات واردة', $date, $box_id, $user_id]);

      DB::commit();
      return redirect('/buy_bills')->with('success', 'تم استيراد العينيات بنجاح');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect('/buy_bills')->with('error', $e->getMessage());
    }
  }

  public function show(Request $request)
  {
    $id = $request['id'];
    $bill = BuyBill::select('id', 'number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'original_balance', 'paid_balance', 'remaining_balance', 'expense', 'byan')->with('buyed_product:id,product_id,quantity,buy_price,total_price,buy_bill_id')->with('buyed_product.product:id,name')->where('id', $id)->first();
    if ($bill != null) {
      $bill_data = view('includes.bill_data', compact('bill'))->render();
      return response()->json(['bill_data' => $bill_data]);
    } else {
      return response(['status' => 'error']);
    }
  }

  public function edit($id)
  {
    $buy_bill = BuyBill::select('id', 'number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'original_balance', 'paid_balance', 'remaining_balance', 'expense', 'byan')->with('buyed_product:id,product_id,quantity,buy_price,total_price,buy_bill_id')->where('id', $id)->first();
    $products = DB::select('SELECT id, name, original_price, quantity FROM products ORDER BY id DESC');
    return view('admin.buy_bill.edit', compact('buy_bill', 'products'));
  }

  public function update(Request $request, $id)
  {

    $paid_balance = abs($request->paid_balance);
    $remaining_balance = $request->remaining_balance;

    $buy_bill = BuyBill::where('id', $id)->first();

    if ($request['tbl'] == null && $buy_bill->paid_balance == $paid_balance && $buy_bill->remaining == $remaining_balance) {
      return redirect('/buy_bills');
    } else {

      DB::beginTransaction();
      try {
        $tblArray = explode(',', $request['tbl']);
        if ($request->tbl != null) {
          for ($i = 0; $i < count($tblArray) / 4; $i++) {

            $buyed_product = new BuyedProduct;
            $buyed_product->product_id = $tblArray[$i * 4 + 0];
            $buyed_product->quantity = $tblArray[$i * 4 + 1];
            $buyed_product->buy_price = $tblArray[$i * 4 + 2];
            $buyed_product->total_price = $tblArray[$i * 4 + 3];
            $buyed_product->buy_bill_id = $id;
            $buyed_product->save();

            $quantity = Quantity::where('product_id', $tblArray[$i * 4 + 0])->get();
            $product = Product::where('id', $tblArray[$i * 4 + 0])->first();

            if ($quantity == null) {

              $quantit = new Quantity;
              $quantit->product_id = $tblArray[$i * 4 + 0];
              $quantit->quantity = $tblArray[$i * 4 + 1];
              $quantit->buy_price = $tblArray[$i * 4 + 2];
              $quantit->save();
            } else if ($quantity != null) {

              $stat = 0;
              foreach ($quantity as $quantityy) {
                if ($tblArray[$i * 4 + 2] == $quantityy->buy_price) {

                  Quantity::where('product_id', $tblArray[$i * 4 + 0])->where('buy_price', $tblArray[$i * 4 + 2])->update([
                    'quantity' => $quantityy->quantity + $tblArray[$i * 4 + 1],
                  ]);
                  $stat = 1;
                }
              }
              if ($stat == 0) {

                $quantit = new Quantity;
                $quantit->product_id = $tblArray[$i * 4 + 0];
                $quantit->quantity = $tblArray[$i * 4 + 1];
                $quantit->buy_price = $tblArray[$i * 4 + 2];
                $quantit->save();
              }
            }

            Product::where('id', $tblArray[$i * 4 + 0])->update([
              'quantity' => $product->quantity + $tblArray[$i * 4 + 1],
              'original_quantity' => $product->original_quantity + $tblArray[$i * 4 + 1],
              'original_price' => $tblArray[$i * 4 + 2],
              'buy_bill_id' => $id,
              'status' => true
            ]);
          }
        }

        DB::statement('UPDATE box SET box.remaining = CASE box.id
                    WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)+?
                    WHEN 6 THEN (SELECT remaining FROM box WHERE box.id = 6)+?
                    ELSE box.remaining
                    END
                WHERE box.id IN(1, 6);', [$buy_bill->paid_balance - $paid_balance, $paid_balance + abs($remaining_balance) - $buy_bill->original_balance]);


        BuyBill::where('id', $id)->update(
          [
            'paid_balance' => $paid_balance,
            'remaining_balance' => $remaining_balance,
            'original_balance' => $remaining_balance + $paid_balance,
            'byan' => $request['byan']
          ]
        );

        if ($request['provider_id'] > 0) {
          $provider = Provider::where('id', $request['provider_id'])->select('balance')->first();
          if ($provider != null) {
            Provider::where('id', $request['provider_id'])->update(['balance' => ($provider->balance - $buy_bill->remaining_balance) +  $remaining_balance]);
          } else {
            throw new Exception('Provider not found');
          }
        } elseif ($request['customer_id'] > 0) {
          $customer = Customer::where('id', $request['customer_id'])->select('balance')->first();
          if ($customer != null) {
            Customer::where('id', $request['customer_id'])->update(['balance' => ($customer->balance - $buy_bill->remaining_balance) +  $request['remaining_balance']]);
          } else {
            throw new Exception('Customer not found');
          }
        } elseif ($request['worker_id'] > 0) {
          $worker = Worker::where('id', $request['worker_id'])->select('balance')->first();
          if ($worker != null) {
            Worker::where('id', $request['worker_id'])->update(['balance' => ($worker->balance - $buy_bill->remaining_balance) +  $request['remaining_balance']]);
          } else {
            throw new Exception('Worker not found');
          }
        }

        DB::commit();
        return redirect('/buy_bills');
      } catch (Exception $e) {
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
      $product = Product::where('id', $buyed_product->product_id)->first();

      $quantity = Quantity::where('product_id', $buyed_product->product_id)->where('buy_price', $buyed_product->buy_price)->first();

      $buy_bill = BuyBill::where('id', $buyed_product->buy_bill_id)->first();

      DB::statement('UPDATE box SET box.remaining = CASE box.id
                WHEN 1 THEN (SELECT remaining FROM box WHERE box.id = 1)+?
                WHEN 6 THEN (SELECT remaining FROM box WHERE box.id = 6)-?
                ELSE box.remaining
                END,
            box.counter = CASE box.id
                WHEN 1 THEN (SELECT counter FROM box WHERE box.id = 1)+1
                ELSE box.counter
                END
            WHERE box.id IN(1, 6, 7);', [$buyed_product->total_price - $buy_bill->expense, $buyed_product->total_price - $buy_bill->expense]);

      $buy_bill = BuyBill::where('id', $buyed_product->buy_bill_id)->update([
        'original_balance' => $buyed_product->buy_bill->original_balance - ($buyed_product->total_price - $buy_bill->expense),
        'paid_balance' => $buyed_product->buy_bill->paid_balance - ($buyed_product->total_price - $buy_bill->expense),
        'expense' => 0
      ]);
      if (($product->quantity - $buyed_product->quantity) == 0) {
        Product::where('id', $buyed_product->product_id)->update([
          'quantity' => $product->quantity - $buyed_product->quantity,
          'original_quantity' => $product->original_quantity - $buyed_product->quantity,
          'status' => false
        ]);
      } else {
        Product::where('id', $buyed_product->product_id)->update([
          'quantity' => $product->quantity - $buyed_product->quantity,
          'original_quantity' => $product->original_quantity - $buyed_product->quantity,
          'status' => true
        ]);
      }


      $buyed_product->delete();
      if ($quantity != null) {
        $q = $quantity->quantity;
        $quantity->update([
          'quantity' => $q - $buyed_product->quantity,
        ]);
      }
      DB::commit();
      return redirect('/buy_bill/edit/' . $buyed_product->buy_bill_id);
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/buy_bills')->with('error', 'Error: ' . $e->getMessage());
    }
  }

  // delete
  public function delete(Request $request)
  {
    // delete buy bill
    $buy_bill = BuyBill::where('id', $request['id'])->first();
    if ($buy_bill != null) {

      $buy_bill->delete();
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'error']);
    }
  }

  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');
    $buy_bills = BuyBill::select('id', 'number', 'date_created', 'byan', 'provider_id', 'customer_id', 'worker_id', 'remaining_balance', 'paid_balance')->with('worker:id,name')->with('customer:id,name')->with('provider:id,name')->whereRaw('date_created >= ? AND date_created <= ?', [$from, $to])->orderBy('id', 'DESC')->get();

    $i = 1;
    $total_rem = 0;
    $total_paid = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;
    $company = config('app.company');

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">' . $company . '</h3><h1 align="center">كشف كل عينيات واردة</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">#</th>
            <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">المستهلك</th>
            <th width="15%" bgcolor="#eee">المبلغ المدفوع</th>
            <th width="10%" bgcolor="#eee">المبلغ المتبقي</th>
            <th width="15%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($buy_bills as $buy_bill) {
      $target = '';
      $balance = '';
      if ($buy_bill->provider_id > 0) {
        $target = $buy_bill->provider->name . ' - داعم';
      } elseif ($buy_bill->customer_id > 0) {
        $target = $buy_bill->customer->name . ' - مستفيد';
      } elseif ($buy_bill->worker_id > 0) {
        $target = $buy_bill->worker->name . ' - موظف';
      }
      if ($buy_bill->remaining_balance > 0) {
        $balance = $buy_bill->remaining_balance . '<span>&#8362;&#160;</span> - دائن -';
      } elseif ($buy_bill->remaining_balance < 0) {
        $balance = $buy_bill->remaining_balance . '<span>&#8362;&#160;</span> - مدين -';
      } else {
        $balance = $buy_bill->remaining_balance . '<span>&#8362;&#160;</span>';
      }
      $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="20%">' . $buy_bill->number . '</td>
              <td width="15%">' . $buy_bill->date_created . '</td>
              <td width="20%">' . $target . '</td>
              <td width="15%">' . $buy_bill->paid_balance . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $balance . '</td>
              <td width="15%">' . $buy_bill->byan . '</td>
            </tr>';
      $total_rem += $buy_bill->remaining_balance;
      $i++;
      $total_paid += $buy_bill->paid_balance;
    }
    if ($total_rem < 0) {
      $total_rem = $total_rem . '<span>&#8362;&#160;</span> - مدين -';
    } elseif ($total_rem > 0) {
      $total_rem = $total_rem . '<span>&#8362;&#160;</span> - دائن -';
    } else {
      $total_rem = $total_rem . '<span>&#8362;&#160;</span>';
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل عينيات واردة');
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
        </tbody></table>');

    PDF::Output('all_buy_bills_' . date('ymdhis') . '.pdf', 'I');
    return response()->json(['status' => 'success']);
  }
}
