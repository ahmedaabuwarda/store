<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Box;
use App\Models\Worker;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\ExportAiniat;
use App\Models\SoldProduct;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Selective;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExportAiniatController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {

    $page = config('app.page');
    $export_ainiats = ExportAiniat::select('id', 'number', 'date_created', 'byan', 'provider_id', 'customer_id', 'worker_id')->with('worker:id,name')->with('customer:id,name')->with('provider:id,name')->orderBy('id', 'DESC')->paginate($page);

    $pages = ceil(ExportAiniat::count() / $page);
    $box = DB::select('SELECT remaining from box where id IN (3,7);');
    if ($request->ajax()) {
      $table = view('admin.export_ainiat.table', compact('export_ainiats'))->render();
      return response()->json(['status' => 'success', 'table' => $table]);
    } else {
      return view('admin.export_ainiat.index', compact('export_ainiats', 'pages', 'box'));
    }
  }

  public function create()
  {
    $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
    $customers = DB::select('SELECT id, name FROM customers ORDER BY id DESC');
    $workers = DB::select('SELECT id, name FROM workers ORDER BY id DESC');
    $products = DB::select('SELECT id, name, quantity FROM products ORDER BY id DESC');
    $boxes = Box::select('id', 'name')->get();

    return view('admin.export_ainiat.create', compact('providers', 'customers', 'workers', 'products', 'boxes'));
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
      $box_id = $request['box_id'];
      $user_id = Auth::user()->id;

      $export_ainiat = new ExportAiniat();
      $export_ainiat->number = $request['number'];
      $export_ainiat->date_created = $request['date_created'];
      if ($request['target'] == 'customers') {
        $customer = Customer::where('id', $customer_id)->select('balance')->first();
        if ($customer != null) {
          Customer::where('id', $customer_id)->update(['balance' => $customer->balance + $remaining_balance]);
          $export_ainiat->customer_id = $customer_id;
        } else {
          DB::rollBack();
          throw new Exception('Customer not found');
        }
      } elseif ($request['target'] == 'providers') {
        $provider = Provider::where('id', $provider_id)->select('balance')->first();
        if ($provider != null) {
          Provider::where('id', $provider_id)->update(['balance' => $provider->balance + $remaining_balance]);
          $export_ainiat->provider_id = $provider_id;
        } else {
          DB::rollBack();
          throw new Exception('Provider not found');
        }
      } elseif ($request['target'] == 'workers') {
        $worker = Worker::where('id', $worker_id)->select('balance')->first();
        if ($worker != null) {
          Worker::where('id', $worker_id)->update(['balance' => $worker->balance + $remaining_balance]);
          $export_ainiat->worker_id = $worker_id;
        } else {
          DB::rollBack();
          throw new Exception('Worker not found');
        }
      }

      $export_ainiat->paid_balance = $paid_balance;
      $export_ainiat->remaining_balance = $remaining_balance;
      $export_ainiat->expense = $request['expense'];
      $export_ainiat->total_balance = abs($remaining_balance) + $paid_balance;
      $export_ainiat->byan = $request['byan'] ?? 'لا يوجد';

      $export_ainiat->total_profit = 0;
      $export_ainiat->save();

      $total_profit = 0;

      $tblArray = explode(',', $request['tbl']);
      for ($i = 0; $i < count($tblArray) / 5; $i++) {

        $product = Product::where('id', $tblArray[$i * 5 + 0])->first();

        if ($product->quantity - $tblArray[$i * 5 + 1] < 0) {
          return redirect('/export_ainiats')->with('error', '1');
        } else if ($product->quantity - $tblArray[$i * 5 + 1] == 0) {

          Product::where('id', $tblArray[$i * 5 + 0])->update([
            'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
            'export_ainiat_id' => $export_ainiat->id,
            'status' => false
          ]);
        } else {
          Product::where('id', $tblArray[$i * 5 + 0])->update([
            'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
            'export_ainiat_id' => $export_ainiat->id
          ]);
        }

        $sold_product = new SoldProduct;
        $sold_product->product_id = $tblArray[$i * 5 + 0];
        $sold_product->quantity = $tblArray[$i * 5 + 1];
        // $sold_product->sell_price = $tblArray[$i * 5 + 2];
        // $sold_product->total_price = $tblArray[$i * 5 + 3];
        // $profit = ($tblArray[$i * 5 + 1] * $tblArray[$i * 5 + 2]) - ($tblArray[$i * 5 + 1] * $product->original_price);
        // $sold_product->profit = $profit;
        // $sold_product->buy_price = $quantity->buy_price;
        $sold_product->export_ainiat_id = $export_ainiat->id;
        // $total_profit += $profit;
        $sold_product->save();

        $s = Selective::where('customer_id', $customer_id)
          ->where('product_id', $tblArray[$i * 5 + 0])
          ->where('status', 0)
          ->first();

        if ($s == null) {
          $selective = new Selective();
          $selective->user_id = $user_id;
          $selective->customer_id = $customer_id;
          $selective->product_id = $tblArray[$i * 5 + 0];
          $selective->status = 1;
          $selective->save();
        } else {
          $s->update([
            'status' => 1,
          ]);
        }

        $selective = Selective::where('customer_id', $customer_id)->where('status', 0)->get();
        if ($selective->count() == 0) {
          Customer::where('id', $customer_id)->update(['status' => 1]);
        }
      }

      ExportAiniat::where('id', $export_ainiat->id)->update(['total_profit' => $total_profit]);

      $date = date($request['date_created'] . ' H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created, movements.box_id, movements.user_id) VALUES (?,0,?,?,?,?)', [$paid_balance, 'فاتورة عينيات صادرة', $date, $box_id, $user_id]);

      DB::commit();
      return redirect('/export_ainiat/edit/' . $export_ainiat->id)->with('success', 'تم تصدير العينيات بنجاح');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect('/export_ainiats')->with('error', $e->getMessage());
      // return redirect('/export_ainiat/edit/' . $export_ainiat->id)->with('error', $e->getMessage());
    }
  }

  public function show(Request $request)
  {
    $id = $request['id'];
    $bill = ExportAiniat::select('id', 'number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'total_balance', 'paid_balance', 'remaining_balance', 'expense', 'byan', 'total_profit')->with('sold_product:id,product_id,quantity,sell_price,total_price,profit,export_ainiat_id')->with('sold_product.product:id,name')->where('id', $id)->first();
    if ($bill != null) {
      $bill_data = view('includes.bill_data', compact('bill'))->render();
      return response()->json(['bill_data' => $bill_data]);
    } else {
      return response(['status' => 'error']);
    }
  }

  public function edit($id)
  {
    $export_ainiat = ExportAiniat::select('id', 'number', 'date_created', 'provider_id', 'customer_id', 'worker_id', 'total_balance', 'paid_balance', 'remaining_balance', 'total_profit', 'expense', 'byan')->with('sold_product:id,product_id,quantity,sell_price,total_price,profit,export_ainiat_id')->where('id', $id)->first();
    $products = DB::select('SELECT id, name, original_price, taqseet_price, quantity FROM products ORDER BY id DESC');
    return view('admin.export_ainiat.edit', compact('export_ainiat', 'products'));
  }

  public function update(Request $request, $id)
  {

    $paid_balance = abs($request->paid_balance);
    $remaining_balance = $request->remaining_balance;
    $customer_id = $request['customer_id'];
    $user_id = Auth::user()->id;

    $export_ainiat = ExportAiniat::where('id', $id)->first();

    DB::beginTransaction();
    try {
      if ($request['tbl'] == null && $export_ainiat->paid_balance == $paid_balance && $export_ainiat->remaining == $remaining_balance) {
        return redirect('/export_ainiats');
      } else {

        $total_profit = 0;
        $tblArray = explode(',', $request['tbl']);

        if ($request->tbl != null) {
          for ($i = 0; $i < count($tblArray) / 5; $i++) {

            $product = Product::where('id', $tblArray[$i * 5 + 0])->first();

            if ($product->quantity - $tblArray[$i * 5 + 1] == 0) {
              Product::where('id', $tblArray[$i * 5 + 0])->update([
                'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                'export_ainiat_id' => $id,
                'status' => false
              ]);
            } else if ($product->quantity - $tblArray[$i * 5 + 1] > 0) {
              Product::where('id', $tblArray[$i * 5 + 0])->update([
                'quantity' => $product->quantity - $tblArray[$i * 5 + 1],
                'export_ainiat_id' => $id,
                'status' => true
              ]);
            } else {
              return redirect('/export_ainiat/edit/' . $id);
            }

            $sold_product = new SoldProduct;
            $sold_product->product_id = $tblArray[$i * 5 + 0];
            $sold_product->quantity = $tblArray[$i * 5 + 1];
            $sold_product->sell_price = $tblArray[$i * 5 + 2];
            $sold_product->total_price = $tblArray[$i * 5 + 3];
            // $profit = ($tblArray[$i * 5 + 1] * $tblArray[$i * 5 + 2]) - ($tblArray[$i * 5 + 1] * $quantity->buy_price);
            // $sold_product->profit = $profit;
            // $sold_product->buy_price = $quantity->buy_price;
            $sold_product->export_ainiat_id = $id;
            // $total_profit += $profit;
            $sold_product->save();

            $s = Selective::where('customer_id', $customer_id)
              ->where('product_id', $tblArray[$i * 5 + 0])
              ->where('status', 0)
              ->first();

            if ($s == null) {
              $selective = new Selective();
              $selective->user_id = $user_id;
              $selective->customer_id = $customer_id;
              $selective->product_id = $tblArray[$i * 5 + 0];
              $selective->status = 1;
              $selective->save();
            } else {
              $s->update([
                'status' => 1,
              ]);
            }

            $selective = Selective::where('customer_id', $customer_id)->where('status', 0)->get();
            if ($selective->count() == 0) {
              Customer::where('id', $customer_id)->update(['status' => 1]);
            }
          }
        }

        ExportAiniat::where('id', $id)->update([
          'paid_balance' => $paid_balance,
          'remaining_balance' => $remaining_balance,
          'total_balance' => abs($remaining_balance) + $paid_balance,
          'total_profit' => $export_ainiat->total_profit + $total_profit,
          'byan' => $request['byan']
        ]);

        if ($request['provider_id'] > 0) {
          $provider = Provider::where('id', $request['provider_id'])->select('balance')->first();
          Provider::where('id', $request['provider_id'])->update(['balance' => ($provider->balance - $export_ainiat->remaining_balance) +  $request['remaining_balance']]);
        } elseif ($request['customer_id'] > 0) {
          $customer = Customer::where('id', $request['customer_id'])->select('balance')->first();
          Customer::where('id', $request['customer_id'])->update(['balance' => ($customer->balance - $export_ainiat->remaining_balance) +  $request['remaining_balance']]);
        } elseif ($request['worker_id'] > 0) {
          $worker = Worker::where('id', $request['worker_id'])->select('balance')->first();
          Worker::where('id', $request['worker_id'])->update(['balance' => ($worker->balance - $export_ainiat->remaining_balance) +  $request['remaining_balance']]);
        }
      }

      DB::commit();
      return redirect('/export_ainiat/edit/' . $id)->with('success', 'تم تحديث الفاتورة بنجاح');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/export_ainiats')->with('error', $e->getMessage());
    }
  }

  public function delete_product($id)
  {

    DB::beginTransaction();
    try {

      $sold_product = SoldProduct::where('id', $id)->with('export_ainiat')->first();
      $product = Product::where('id', $sold_product->product_id)->first();

      $profit = ($sold_product->sell_price * $sold_product->quantity) - ($sold_product->buy_price * $sold_product->quantity);

      $export_ainiat = ExportAiniat::where('id', $sold_product->export_ainiat_id)->update([
        'total_balance' => $sold_product->export_ainiat->total_balance - $sold_product->total_price,
        'paid_balance' => $sold_product->export_ainiat->paid_balance - $sold_product->total_price,
        'total_profit' => $sold_product->export_ainiat->total_profit - $profit
      ]);

      Product::where('id', $sold_product->product_id)->update([
        'quantity' => $product->quantity + $sold_product->quantity,
        'status' => true
      ]);

      $sold_product->delete();

      DB::commit();
      return redirect('/export_ainiat/edit/' . $sold_product->export_ainiat_id);
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect('/export_ainiats')->with('error', 'Error: ' . $e->getMessage());
    }
  }

  // delete
  public function delete(Request $request)
  {
    // delete export_ainiat
    $export_ainiat = ExportAiniat::where('id', $request['id'])->first();
    if ($export_ainiat != null) {

      $export_ainiat->delete();
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'error']);
    }
  }

  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');
    $export_ainiats = ExportAiniat::select('id', 'number', 'date_created', 'byan', 'provider_id', 'customer_id', 'worker_id', 'remaining_balance', 'paid_balance', 'total_profit')->with('worker:id,name')->with('customer:id,name')->with('provider:id,name')->whereRaw('date_created >= ? AND date_created <= ?', [$from, $to])->orderBy('id', 'DESC')->get();

    $i = 1;
    $total_rem = 0;
    $total_profit = 0;
    $total_paid = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;
    $company = config('app.company');

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">' . $company . '</h3><h1 align="center">كشف كل عينيات صادرة</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">#</th>
            <th width="15%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">المستفيد</th>
            <th width="10%" bgcolor="#eee">المبلغ المدفوع</th>
            <th width="10%" bgcolor="#eee">المبلغ المتبقي</th>
            <th width="10%" bgcolor="#eee">المربح</th>
            <th width="15%" bgcolor="#eee">الملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($export_ainiats as $export_ainiat) {
      $target = '';
      $balance = '';
      if ($export_ainiat->provider_id > 0) {
        $target = $export_ainiat->provider->name . ' - داعم';
      } elseif ($export_ainiat->customer_id > 0) {
        $target = $export_ainiat->customer->name . ' - مستفيد';
      } elseif ($export_ainiat->worker_id > 0) {
        $target = $export_ainiat->worker->name . ' - موظف';
      }
      if ($export_ainiat->remaining_balance > 0) {
        $balance = $export_ainiat->remaining_balance . '<span>&#8362;&#160;</span> - دائن -';
      } elseif ($export_ainiat->remaining_balance < 0) {
        $balance = $export_ainiat->remaining_balance . '<span>&#8362;&#160;</span> - مدين -';
      } else {
        $balance = $export_ainiat->remaining_balance . '<span>&#8362;&#160;</span>';
      }
      $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="15%">' . $export_ainiat->number . '</td>
              <td width="15%">' . $export_ainiat->date_created . '</td>
              <td width="20%">' . $target . '</td>
              <td width="10%">' . $export_ainiat->paid_balance . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $balance . '</td>
              <td width="10%">' . $export_ainiat->total_profit . '<span>&#8362;&#160;</span></td>
              <td width="15%">' . $export_ainiat->byan . '</td>
            </tr>';
      $total_rem += $export_ainiat->remaining_balance;
      $total_profit += $export_ainiat->total_profit;
      $total_paid += $export_ainiat->paid_balance;
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
    PDF::SetTitle('كل عينيات صادرة');
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

    PDF::Output('all_export_ainiats_' . date('ymdhis') . '.pdf', 'I');
    return response()->json(['status' => 'success']);
  }
}
