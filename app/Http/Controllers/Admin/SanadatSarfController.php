<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Worker;
use App\Models\Box;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\Sanadat_Sarf;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Imports\SanadatSarfImport;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class SanadatSarfController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $page = config('app.page');
    $sanadat_sarfs = Sanadat_Sarf::select('id', 'number', 'date_created', 'balance', 'byan', 'provider_id', 'customer_id', 'worker_id', 'box_id')->with('worker:id,name')->with('customer:id,name')->with('provider:id,name')->with('box:id,name,currency_id')->orderBy('date_created', 'DESC')->paginate($page);

    $boxes = Box::select('id', 'name')->get();

    // if the request is ajax
    if ($request->ajax()) {

      $table = view('admin.sanadat_sarf.table', compact('sanadat_sarfs'))->render();
      return response()->json(['table' => $table]);

      // if the request is not ajax
    } else {

      $customers = DB::select('SELECT id, name FROM customers ORDER BY id DESC');
      $providers = DB::select('SELECT id, name FROM providers ORDER BY id DESC');
      $workers = DB::select('SELECT id, name FROM workers ORDER BY id DESC');
      $pages = ceil(Sanadat_Sarf::count() / $page);
      return view('admin.sanadat_sarf.index', compact('sanadat_sarfs', 'customers', 'providers', 'workers', 'pages', 'boxes'));
    }
  }

  public function store(Request $request)
  {

    $user_id = Auth::user()->id;
    $balance = abs($request['balance']);
    $customer_id = $request['customer_id'];
    $provider_id = $request['provider_id'];
    $worker_id = $request['worker_id'];
    $number = $request['number'];
    $date_created = $request['date_created'];
    $byan = $request['byan'] ?? 'لا يوجد';
    $target = $request['target'] ?? '';
    $box_id = $request['box_id'];

    $file_attachment = $request->file('file_attachment');

    $xlsx_data = null;

    if ($file_attachment != null && $target == 'customers') {
      // Read the xlsx file
      $xlsx_data = Excel::toArray(new SanadatSarfImport, $file_attachment);

      // Assuming we have only one sheet in the Excel file, so we'll take the first sheet
      $sheetData = $xlsx_data[0];

      // Start transaction
      DB::beginTransaction();
      try {
        // Loop through the rows, skip the first row (headers)
        for ($i = 1; $i < count($sheetData); $i++) {
          $row = $sheetData[$i];
          // dd($row[1]);
          // Ensure the row is valid (not empty)
          if ($row[0] != null) {
            // Data from the row
            $number = date('ymdhis' . $i);  // Unique number per row
            $excel_date = $row[1]; // The Excel serial date is in the second column (adjust if necessary)
            $date_created = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($excel_date - 2)->format('Y-m-d');

            $customer = Customer::select('id', 'name', 'balance')->where('name', $row[2])->first();

            // Ensure customer exists before processing
            if ($customer) {
              $customer_id = $customer->id;
              $balance = abs($row[3]);
              $byan = $row[4];

              // Create a new Sanadat_Sarf record
              $sanadat_sarf = new Sanadat_Sarf;
              $sanadat_sarf->number = $number;
              $sanadat_sarf->date_created = $date_created;
              $sanadat_sarf->balance = $balance;
              $sanadat_sarf->box_id = $box_id;
              $sanadat_sarf->byan = $byan;

              // Update customer balance
              $customer->update(['balance' => $customer->balance - $balance]);
              $sanadat_sarf->customer_id = $customer_id;

              // Save the Sanadat_Sarf record
              $sanadat_sarf->save();

              // Update the box balance and counter
              $box = Box::select('id', 'balance', 'counter')->where('id', $box_id)->first();
              $box->update([
                'balance' => $box->balance - $balance,
                'counter' => $box->counter + 1,
              ]);

              // Insert into movements table
              $date = date($date_created . ' H:i:s');
              DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,0,?,?,?,?)', [$balance, 'سند صرف - ' . $customer->name, $date, $box_id, $user_id]);
            }
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة سند الصرف بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        return dd($e->getMessage());  // For debugging
        return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ السند']);
      }
    } else if ($balance != null) {
      DB::beginTransaction();
      try {

        $sanadat_sarf = new Sanadat_Sarf;
        $sanadat_sarf->number = $number;
        $sanadat_sarf->date_created = $date_created;
        $sanadat_sarf->balance = $balance;
        $sanadat_sarf->box_id = $box_id;
        $sanadat_sarf->byan = $byan;

        if ($target == 'customers') {
          $customer = Customer::where('id', $customer_id)->select('name', 'balance')->first();
          if ($customer != null) {
            Customer::where('id', $customer_id)->update(['balance' => $customer->balance - $balance]);
            $sanadat_sarf->customer_id = $customer_id;
            $target = $customer->name;
          } else {
            DB::rollback();
            return response()->json(['status' => 'error']);
          }
        } elseif ($target == 'providers') {
          $provider = Provider::where('id', $provider_id)->select('name', 'balance')->first();
          if ($provider != null) {
            Provider::where('id', $provider_id)->update(['balance' => $provider->balance - $balance]);
            $sanadat_sarf->provider_id = $provider_id;
            $target = $provider->name;
          } else {
            DB::rollback();
            return response()->json(['status' => 'error']);
          }
        } elseif ($target == 'workers') {
          $worker = Worker::where('id', $worker_id)->select('name', 'balance')->first();
          if ($worker != null && ($worker->balance - $balance) >= -500 && ($worker->balance - $balance) <= 500) {
            Worker::where('id', $worker_id)->update(['balance' => $worker->balance - $balance]);
            $sanadat_sarf->worker_id = $worker_id;
            $target = $worker->name;
          } else {
            DB::rollback();
            return response()->json(['status' => 'error']);
          }
        }

        $sanadat_sarf->save();

        $box = Box::select('id', 'balance', 'counter')->where('id', $box_id)->first();
        if ($box->balance < $balance) {
          return response()->json(['status' => 'error', 'message' => 'لا يوجد لديك رصيد كاف!']);
        }
        $box->update([
          'balance' => $box->balance - $balance,
          'counter' => $box->counter + 1,
        ]);

        $date = date($date_created . ' H:i:s');
        DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,0,?,?,?,?)', [$balance, 'سند صرف', $date, $box_id, $user_id]);

        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة سند الصرف بنجاح']);
      } catch (Exception $e) {
        DB::rollback();
        return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ السند']);
      }
    }
    return response()->json(['status' => 'error', 'message' => 'حدث خطأ بعض الحقول مفقودة!']);
  }

  public function delete(Request $request)
  {
    $user_id = Auth::user()->id;

    DB::beginTransaction();
    $id = $request['id'];
    $sanadat_sarf = Sanadat_Sarf::where('id', $id)->first();
    try {
      $customer_id = $sanadat_sarf->customer_id;
      $provider_id = $sanadat_sarf->provider_id;
      $worker_id = $sanadat_sarf->worker_id;
      $balance = $sanadat_sarf->balance;
      $box_id = $sanadat_sarf->box->id;

      if ($sanadat_sarf != null && $provider_id > 0) {
        $provider = Provider::where('id', $provider_id)->select('balance')->first();
        if ($provider != null) {
          Provider::where('id', $provider_id)->update(['balance' => $provider->balance + $balance]);
        } else {
          return response()->json(['status' => 'error']);
        }
      } elseif ($sanadat_sarf != null && $customer_id > 0) {
        $customer = Customer::where('id', $customer_id)->select('balance')->first();
        if ($customer != null) {
          Customer::where('id', $customer_id)->update(['balance' => $customer->balance + $balance]);
        } else {
          return response()->json(['status' => 'error']);
        }
      } elseif ($sanadat_sarf != null && $worker_id > 0) {
        $worker = Worker::where('id', $worker_id)->select('balance')->first();
        if ($worker != null) {
          Worker::where('id', $worker_id)->update(['balance' => $worker->balance + $balance]);
        } else {
          return response()->json(['status' => 'error']);
        }
      } else {
        return response()->json(['status' => 'error']);
      }

      $box = Box::select('id', 'balance', 'counter')->where('id', $box_id)->first();

      $box->update([
        'balance' => $box->balance + $balance,
        'counter' => $box->counter + 1,
      ]);

      $date = date('Y-m-d H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,1,?,?,?,?)', [$balance, 'سند صرف', $date, $box_id, $user_id]);

      $sanadat_sarf->delete();
      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
      DB::rollback();
      return response()->json(['status' => 'error']);
    }
  }

  public function to_pdf(Request $request)
  {
    $from = $request['from'];
    $to = $request['to'];
    $sanadat_sarfs = Sanadat_Sarf::select('id', 'number', 'date_created', 'balance', 'byan', 'provider_id', 'customer_id', 'worker_id')->with('user:id,name')->with('customer:id,name')->with('provider:id,name')->whereRaw('date_created >= ? AND date_created <= ?', [$from, $to])->orderBy('id', 'DESC')->get();

    $i = 1;
    $total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;
    $company = config('app.company');

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">' . $company . '</h3><h1 align="center">كشف كل سندات الصرف</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="20%" bgcolor="#eee">رقم السند</th>
            <th width="20%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">المستهلك</th>
            <th width="10%" bgcolor="#eee">الرصيد</th>
            <th width="20%" bgcolor="#eee">البيان</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($sanadat_sarfs as $sanadat_sarf) {
      $target = '';
      if ($sanadat_sarf->provider_id > 0) {
        $target = $sanadat_sarf->provider->name . ' - داعم';
      } elseif ($sanadat_sarf->customer_id > 0) {
        $target = $sanadat_sarf->customer->name . ' - مستفيد';
      } elseif ($sanadat_sarf->worker_id > 0) {
        $target = $sanadat_sarf->worker->name . ' - موظف';
      }
      $table_content .= '<tr>
              <td width="10%">' . $i . '</td>
              <td width="20%">' . $sanadat_sarf->number . '</td>
              <td width="20%">' . $sanadat_sarf->date_created . '</td>
              <td width="20%">' . $target . '</td>
              <td width="10%">' . $sanadat_sarf->balance . '<span>&#8362;&#160;</span></td>
              <td width="20%">' . $sanadat_sarf->byan . '</td>
            </tr>';
      $total += $sanadat_sarf->balance;
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل سندات الصرف');
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

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%" color="#fff" bgcolor="#DB2E39">' . $total . '<span>&#8362;&#160;</span></td></tr></tbody></table>');
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/sanadat_sarf');
    // $directoryPath = '/media/ahmed/Downloads';
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف سندات الصرف_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }

    // Return the file as a download response
    // return redirect($filePath);
    // PDF::Output('all_sanadat_sarfs_' . date('ymdhis') . '.pdf', 'D');
    // return response()->json(['status' => 'success']);
  }

  public function to_xlsx(Request $request) {

    $fileName = 'كشف سندات الصرف_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/sanadat_sarf');
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new \App\Exports\SanadatSarfExport(), 'xlsx/sanadat_sarf/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
