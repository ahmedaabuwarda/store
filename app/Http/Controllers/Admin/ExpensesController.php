<?php

namespace App\Http\Controllers\Admin;

use PDF;

use App\Models\Box;
use App\Models\Expense;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExpensesController extends Controller
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
    $expenses = Expense::select('id', 'date_created', 'balance', 'notes', 'user_id', 'box_id')
      ->orderBy('date_created', 'DESC')
      ->with(['box:id,name,currency_id', 'box.currency:id,name,symbol', 'user:id,name'])
      ->paginate($page);
    $users = DB::select('SELECT id, name FROM users ORDER BY created_at DESC');
    $boxes = Box::select('id', 'balance', 'remaining', 'name')->get();
    // if the request is ajax
    if ($request->ajax()) {
      $table = view('admin.expense.table', compact('expenses'))->render();
      return response()->json(['table' => $table]);
      // if the request is not ajax
    } else {
      $pages = ceil(Expense::count() / $page);
      return view('admin.expense.index', compact('expenses', 'pages', 'users', 'boxes'));
    }
  }

  // store
  public function store(Request $request)
  {
    $balance = $request->balance;
    $box_id = $request->box_id;
    $user_id = Auth::user()->id;

    DB::beginTransaction();
    try {
      $balance = abs($request->balance);
      $expense = new Expense;
      $expense->balance = $balance;
      $expense->date_created = $request->date_created;
      $expense->user_id = $user_id;
      $expense->box_id = $box_id;
      $expense->notes = $request->notes ?? 'لا يوجد';

      $expense->save();

      $box = Box::select('id', 'balance', 'remaining', 'counter')->where('id', $box_id)->first();
      if ($box->balance < $balance) {
        return response()->json(['status' => 'success', 'message' => 'عذرا ليس لديك رصيد كاف!']);
      }
      $box->update([
        'balance' => $box->balance - $balance,
        'remaining' => $box->remaining - $balance,
        'counter' => $box->counter + 1
      ]);

      $date = date($request['date_created'] . ' H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created, movements.box_id, movements.user_id) VALUES (?,0,?,?,?,?)', [$balance, 'مصروف', $date, $box_id, $user_id]);

      DB::commit();
      return response()->json(['status' => 'success', 'message' => 'تمت الاضافة بنجاح']);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  // to pdf
  public function to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');
    $user_id = $request->user_id;

    if ($user_id == "all") {
      $expenses = Expense::select('id', 'date_created', 'balance', 'notes', 'user_id', 'box_id')
        ->whereBetween('date_created', [$from, $to])
        // ->where('user_id', $user_id)
        ->orderBy('date_created', 'DESC')
        ->with(['box:id,name,currency_id', 'box.currency:id,name,symbol', 'user:id,name'])
        ->get();
    } else {
      $expenses = Expense::select('id', 'date_created', 'balance', 'notes', 'user_id', 'box_id')
        ->whereBetween('date_created', [$from, $to])
        ->where('user_id', $user_id)
        ->orderBy('date_created', 'DESC')
        ->with(['box:id,name,currency_id', 'box.currency:id,name,symbol', 'user:id,name'])
        ->get();
    }

    $i = 1;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل المصاريف</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="30%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">بواسطة</th>
            <th width="10%" bgcolor="#eee">المبلغ</th>
            <th width="15%" bgcolor="#eee">الصندوق</th>
            <th width="15%" bgcolor="#eee">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($expenses as $expense) {
      $table_content .= '<tr>
              <td width="10%">' . $i . '</td>
              <td width="30%">' . $expense->date_created . '</td>
              <td width="20%">' . $expense->user->name . '</td>
              <td width="10%">' . $expense->balance . ' ' . $expense->box->currency->symbol . '</td>
              <td width="15%">' . $expense->box->name . '</td>
              <td width="15%">' . $expense->notes . '</td>
            </tr>';
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل المصاريف');
    PDF::SetAuthor($by);
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
    PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
    PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

    PDF::AddPage();
    PDF::writeHTML($content);
    PDF::SetFont('freeserif', '', 11);
    PDF::writeHTML($table_content);
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/المصاريف' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }
    // Save the file to the storage folder
    $filePath = $directoryPath . '/' . 'كشف مصاريف' . '_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('provider_kashf_hisab_' . date('ymdhis') . '.pdf', 'D');
    return response()->json(['status' => 'success']);
  }

  // to xlsx
  public function to_xlsx(Request $request)
  {
    // dd(date($request->from . ' 00:00:00'));
    $fileName = 'كشف المصاريف_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/المصاريف' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new ExpensesExport(), 'xlsx/المصاريف' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
