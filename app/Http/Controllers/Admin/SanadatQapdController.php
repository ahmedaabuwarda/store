<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Worker;
use App\Models\Box;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\Sanadat_Qapd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;

class SanadatQapdController extends Controller
{

  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  // index
  public function index(Request $request)
  {
    $page = config('app.page');
    $sanadat_qapds = Sanadat_Qapd::select('id', 'number', 'date_created', 'balance', 'notes', 'provider_id', 'customer_id', 'worker_id', 'box_id', 'user_id')
      ->with([
        'worker:id,name',
        'customer:id,name',
        'provider:id,name',
        'box:id,name,currency_id',
        'user:id,name',
      ])
      ->orderBy('date_created', 'DESC')
      ->paginate($page);

    $boxes = Box::select('id', 'name', 'balance')->get();

    // if the request is ajax
    if ($request->ajax()) {

      $table = view('admin.sanadat_qapd.table', compact('sanadat_qapds'))->render();
      return response()->json(['table' => $table]);

      // if the request is not ajax
    } else {

      $customers = DB::select('SELECT id, name FROM customers ORDER BY id DESC');
      $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
      $workers = DB::select('SELECT id, name FROM workers ORDER BY id DESC');
      $pages = $sanadat_qapds->lastPage();
      return view('admin.sanadat_qapd.index', compact('sanadat_qapds', 'customers', 'providers', 'workers', 'pages', 'boxes'));
    }
  }

  // store
  public function store(Request $request)
  {
    $user_id = Auth::user()->id;
    $box_id = $request['box_id'];
    DB::beginTransaction();
    try {

      $balance = abs($request['balance']);
      $customer_id = $request['customer_id'];
      $provider_id = $request['provider_id'];
      $worker_id = $request['worker_id'];
      $target = '';

      $sanadat_qapd = new Sanadat_Qapd;
      $sanadat_qapd->number = $request['number'];
      $sanadat_qapd->date_created = $request['date_created'];
      $sanadat_qapd->balance = $balance;
      $sanadat_qapd->box_id = $box_id;
      $sanadat_qapd->user_id = $user_id;
      $sanadat_qapd->notes = $request['notes'] ?? 'لا يوجد';

      if ($request['target'] == 'customers') {
        $customer = Customer::where('id', $customer_id)->select('name', 'balance')->first();
        if ($customer != null) {
          Customer::where('id', $customer_id)->update(['balance' => $customer->balance + $balance]);
          $sanadat_qapd->customer_id = $customer_id;
          $target = $customer->name;
        } else {
          DB::rollback();
          return response()->json(['status' => 'error']);
        }
      } elseif ($request['target'] == 'providers') {

        $provider = Provider::where('id', $provider_id)->select('name', 'balance')->first();
        if ($provider != null) {
          Provider::where('id', $provider_id)->update(['balance' => $provider->balance + $balance]);
          $sanadat_qapd->provider_id = $provider_id;
          $target = $provider->name;
        } else {
          DB::rollback();
          return response()->json(['status' => 'error']);
        }
      } elseif ($request['target'] == 'workers') {

        $worker = Worker::where('id', $worker_id)->select('name', 'balance')->first();
        if ($worker != null) {
          Worker::where('id', $worker_id)->update(['balance' => $worker->balance + $balance]);
          $sanadat_qapd->worker_id = $worker_id;
          $target = $worker->name;
        } else {
          DB::rollback();
          return response()->json(['status' => 'error']);
        }
      }
      $sanadat_qapd->save();

      $box = Box::select('id', 'balance', 'counter')->where('id', $box_id)->first();

      $box->update([
        'balance' => $box->balance + $balance,
        'counter' => $box->counter + 1,
      ]);

      $date = date($request['date_created'] . ' H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,1,?,?,?,?)', [$balance, 'سند قبض - ' . $target, $date, $box_id, $user_id]);

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollback();
      return response()->json(['status' => 'error' . $e->getMessage()]);
    }
  }

  public function delete(Request $request)
  {
    $user_id = Auth::user()->id;

    DB::beginTransaction();
    $id = $request['id'];
    $sadat_qapd = Sanadat_Qapd::where('id', $id)->first();
    try {
      $customer_id = $sadat_qapd->customer_id;
      $provider_id = $sadat_qapd->provider_id;
      $worker_id = $sadat_qapd->worker_id;
      $balance = $sadat_qapd->balance;
      $box_id = $sadat_qapd->box->id;

      if ($sadat_qapd != null && $provider_id > 0) {
        $provider = Provider::where('id', $provider_id)->select('balance')->first();
        if ($provider != null) {
          Provider::where('id', $provider_id)->update(['balance' => $provider->balance - $balance]);
        } else {
          DB::rollback();
          return response()->json(['status' => 'error']);
        }
      } elseif ($sadat_qapd != null && $customer_id > 0) {
        $customer = Customer::where('id', $customer_id)->select('balance')->first();
        if ($customer != null) {
          Customer::where('id', $customer_id)->update(['balance' => $customer->balance - $balance]);
        } else {
          DB::rollback();
          return response()->json(['status' => 'error']);
        }
      } elseif ($sadat_qapd != null && $worker_id > 0) {
        $worker = Worker::where('id', $worker_id)->select('balance')->first();
        if ($worker != null) {
          Worker::where('id', $worker_id)->update(['balance' => $worker->balance - $balance]);
        } else {
          DB::rollback();
          return response()->json(['status' => 'error']);
        }
      } else {
        return response()->json(['status' => 'error']);
      }

      $box = Box::select('id', 'balance', 'counter')->where('id', $box_id)->first();

      $box->update([
        'balance' => $box->balance - $balance,
        'counter' => $box->counter + 1,
      ]);

      $date = date('Y-m-d H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,0,?,?,?,?)', [$balance, 'حذف سند قبض', $date, $box_id, $user_id]);

      $sadat_qapd->delete();
      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollback();
      return response()->json(['status' => 'error']);
    }
  }

  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');
    $sanadat_qapds = Sanadat_Qapd::select('id', 'number', 'date_created', 'balance', 'notes', 'provider_id', 'customer_id', 'worker_id', 'box_id', 'user_id')
      ->with([
        'user:id,name',
        'box:id,name,currency_id',
        'box.currency:id,symbol',
        'customer:id,name',
        'provider:id,name',
        'worker:id,name'
      ])
      ->whereRaw('date_created >= ? AND date_created <= ?', [$from, $to])
      ->orderBy('id', 'DESC')
      ->get();

    $i = 1;
    $total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل سندات القبض</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">الرقم</th>
            <th width="15%" bgcolor="#eee">رقم السند</th>
            <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="15%" bgcolor="#eee">الزبون</th>
            <th width="10%" bgcolor="#eee">المبلغ</th>
            <th width="10%" bgcolor="#eee">الصندوق</th>
            <th width="10%" bgcolor="#eee">بواسطة</th>
            <th width="20%" bgcolor="#eee">الملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($sanadat_qapds as $sanadat_qapd) {
      $target = '';
      if ($sanadat_qapd->provider_id > 0) {
        $target = $sanadat_qapd->provider->name . ' - داعم';
      } elseif ($sanadat_qapd->customer_id > 0) {
        $target = $sanadat_qapd->customer->name . ' - زبون';
      } elseif ($sanadat_qapd->worker_id > 0) {
        $target = $sanadat_qapd->worker->name . ' - موظف';
      }
      $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="15%">' . $sanadat_qapd->number . '</td>
              <td width="15%">' . $sanadat_qapd->date_created . '</td>
              <td width="15%">' . $target . '</td>
              <td width="10%">' . $sanadat_qapd->balance . ' ' . $sanadat_qapd->box->currency->symbol . '</td>
              <td width="10%">' . $sanadat_qapd->box->name . '</td>
              <td width="10%">' . $sanadat_qapd->user->name . '</td>
              <td width="20%">' . $sanadat_qapd->notes . '</td>
            </tr>';
      $total += $sanadat_qapd->balance;
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل سندات القبض');
    PDF::SetAuthor($by);
    // set some language dependent data:
    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $lg['w_page'] = 'page';
    PDF::SetPageOrientation('L', 'P');
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

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%" color="#fff" bgcolor="#003B36">' . $total . '<span>&#8362;&#160;</span></td></tr></tbody></table>');
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/سندات القبض' . '/' . date('Y-m-d'));
    // $directoryPath = '/media/ahmed/Downloads';
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف سندات القبض_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('all_sanadat_qapd_' . date('ymdhis') . '.pdf', 'D');
    // return response()->json(['status' => 'success']);
  }

  public function to_xlsx(Request $request)
  {

    $fileName = 'كشف سندات القبض_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/سندات القبض' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new \App\Exports\SanadatQapdExport(), 'xlsx/سندات القبض/' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
