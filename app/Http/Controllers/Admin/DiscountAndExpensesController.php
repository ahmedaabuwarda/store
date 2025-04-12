<?php

namespace App\Http\Controllers\Admin;

use PDF;

use App\Models\Box;

use App\Models\User;
use App\Models\Discount;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountStoreRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DiscountAndExpensesController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $page = config('app.page');
    $discounts = Discount::select('id', 'date_created', 'balance', 'notes', 'done_by', 'box_id')->orderBy('date_created', 'DESC')->with('box')->paginate($page);
    $users = DB::select('SELECT id, name FROM users ORDER BY created_at DESC');
    $box = DB::select('SELECT remaining FROM box WHERE id = 2 ORDER BY created_at DESC');
    $boxes = Box::select('id', 'balance', 'remaining', 'name')->get();
    // if the request is ajax
    if ($request->ajax()) {
      $table = view('admin.discount.table', compact('discounts'))->render();
      return response()->json(['table' => $table]);
      // if the request is not ajax
    } else {
      $pages = ceil(Discount::count() / $page);
      return view('admin.discount.index', compact('discounts', 'pages', 'users', 'box', 'boxes'));
    }
  }

  public function store(DiscountStoreRequest $request)
  {
    $balance = $request->balance;
    $box_id = $request->box_id;
    $user_id = Auth::user()->id;

    DB::beginTransaction();
    try {
      $balance = abs($request->balance);
      $discount = new Discount;
      $discount->balance = $balance;
      $discount->date_created = $request->date_created;
      $discount->done_by = $request->done_by;
      $discount->box_id = $box_id;
      $discount->notes = $request->notes ?? 'لا يوجد';

      $discount->save();

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

  public function to_pdf(Request $request)
  {
    $from = $request->from;
    $to = $request->to;
    $done_by = $request->done_by;

    if ($done_by == "all") {
      $discounts = DB::select('SELECT date_created, balance, notes, done_by from discounts where date_created >= :from AND date_created <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);
    } else {
      $discounts = DB::select('SELECT date_created, balance, notes, done_by from discounts where date_created >= :from AND date_created <= :to AND done_by = :done_by ORDER BY id DESC', ['from' => $from, 'to' => $to, 'done_by' => $done_by]);
    }

    $i = 1;
    $total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;
    $company = config('app.company');

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">' . $company . '</h3><h1 align="center">كشف كل المصاريف</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '<p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="30%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="20%" bgcolor="#eee">بواسطة</th>
            <th width="10%" bgcolor="#eee">المبلغ</th>
            <th width="30%" bgcolor="#eee">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($discounts as $discount) {
      $table_content .= '<tr>
              <td width="10%">' . $i . '</td>
              <td width="30%">' . $discount->date_created . '</td>
              <td width="20%">' . $discount->done_by . '</td>
              <td width="10%">' . $discount->balance . '</td>
              <td width="30%">' . $discount->notes . '</td>
            </tr>';
      $total += $discount->balance;
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل المصاريف');
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
    PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
    PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

    PDF::AddPage();
    PDF::writeHTML($content);
    PDF::SetFont('freeserif', '', 11);
    PDF::writeHTML($table_content);

    // PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%"></td><td width="40%">المجموع</td><td width="10%" color="#fff" bgcolor="#003B36">' . $total . '<span>&#8362;&#160;</span></td></tr></tbody></table>');
    // PDF::Output('all_discounts.pdf', 'I');
    // return response()->json(['status' => 'success']);
    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/المصاريف' . '/' . date('Y-m-d'));
    // $directoryPath = '/media/ahmed/Downloads';
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/' . 'كشف مصاريف' . '_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('provider_kashf_hisab_' . date('ymdhis') . '.pdf', 'D');
    return response()->json(['status' => 'success']);
  }
}
