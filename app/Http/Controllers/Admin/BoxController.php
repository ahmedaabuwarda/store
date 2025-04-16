<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Box;
use App\Models\Movement;

use App\Exports\BoxExport;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;

class BoxController extends Controller
{
  //
  public function __construct()
  {
    $this->middleware('auth');
  }

  // index
  public function index(Request $request)
  {
    $page = config('app.page');
    $currencies = Currency::select('id', 'name')->orderBy('id', 'DESC')->get();
    $boxes = Box::select('id', 'name', 'currency_id', 'balance', 'created_at')->with('currency')->orderBy('id', 'DESC')->paginate($page);

    if ($request->ajax()) {
      $table = view('admin.box.table', compact('boxes'))->render();
      return response()->json(['table' => $table]);
    } else {
      $pages = ceil(Box::count() / $page);
      return view('admin.box.index', compact('boxes', 'pages', 'currencies'));
    }
  }

  // store
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'currency_id' => 'required|exists:currencies,id',
    ]);

    DB::beginTransaction();
    try {

      $box = new Box();
      $box->name = $request->name;
      $box->currency_id = $request->currency_id;
      $box->balance = 0;
      $box->save();

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      // return response()->json(['status' => 'error']);
      // return dd($e->getMessage());
      return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
    }
  }

  // edit
  public function edit($id)
  {
    $box = Box::select('id', 'name', 'currency_id', 'balance')->where('id', $id)->with('currency')->first();
    $currencies = Currency::select('id', 'name', 'symbol')->get();
    return view('admin.box.edit', compact('box', 'currencies'));
  }

  // update
  public function update(Request $request)
  {

    $request->validate([
      'id' => 'required|numeric',
      'name' => 'required|string|min:5',
      'currency_id' => 'required|numeric',
    ]);

    $box = Box::select('id', 'name', 'currency_id')->where('id', $request->id)->first();

    $box->update([
      'name' => $request->name,
      'currency_id' => $request->currency_id,
    ]);

    return redirect('/boxes')->with('success', 'تم تحديث الصندوق بنجاح');
  }

  // convert
  public function convert(Request $request)
  {
    // dd($request);
    $request->validate([
      'box_from' => 'required',
      'box_to' => 'required',
      'balance' => 'required|min:0',
      'convert_price' => 'required|min:0',
      'operation' => 'required',
    ]);

    $box_from = $request->box_from;
    $box_to = $request->box_to;
    $balance = $request->balance;
    $convert_price = $request->convert_price;
    $operation = $request->operation;
    $user_id = Auth::user()->id;

    DB::beginTransaction();
    try {

      $box_from = Box::select('id', 'name', 'currency_id', 'balance')->where('id', $box_from)->first();
      $box_to = Box::select('id', 'name', 'currency_id', 'balance')->where('id', $box_to)->first();

      if ($box_from->balance < $balance) {
        return response()->json(['status' => 'error', 'error' => 'لا يوجد لديك رصيد كاف!']);
      }
      $box_from->update([
        'balance' => $box_from->balance - $balance,
      ]);
      $original_balance = $balance;
      if ($operation == '*') {
        $balance = $balance * $convert_price;
      } else {
        $balance = $balance / $convert_price;
      }
      $box_to->update([
        'balance' => $box_to->balance + $balance,
      ]);

      $date = date('Y-m-d H:i:s');
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,0,?,?,?,?)', [$original_balance, 'تحويل من صندوق - ' . $box_from->name, $date, $box_from->id, $user_id]);
      DB::insert('INSERT INTO movements (movements.balance, movements.type, movements.from, movements.date_created,movements.box_id,movements.user_id) VALUES (?,1,?,?,?,?)', [$balance, 'تحويل الى صندوق - ' . $box_to->name, $date, $box_to->id, $user_id]);

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      // return response()->json(['status' => 'error']);
      // return dd($e->getMessage());
      return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
    }
  }

  public function to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');
    $box_id = $request->box_id;

    if ($box_id == "all") {

      $boxes = Box::whereBetween('created_at', [$from, $to])
        ->orderBy('id', 'DESC')
        ->get();

      $i = 1;
      $time = date('H:i:s');
      $date = date('Y-m-d');
      $by = Auth::user()->name;

      $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل الصناديق</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
      $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="20%" bgcolor="#eee">الرقم</th>
            <th width="20%" bgcolor="#eee">التاريخ</th>
            <th width="20%" bgcolor="#eee">الاسم</th>
            <th width="20%" bgcolor="#eee">العملة</th>
            <th width="20%" bgcolor="#eee">الرصيد</th>
          </tr>
        </thead>
        <tbody>';
      foreach ($boxes as $box) {
        $table_content .= '<tr>
              <td width="20%">' . $i . '</td>
              <td width="20%">' . $box->created_at . '</td>
              <td width="20%">' . $box->name . '</td>
              <td width="20%">' . $box->currency->name . '</td>
              <td width="20%">' . $box->balance . ' ' . $box->currency->symbol . '</td>
            </tr>';
        $i++;
      }

      $table_content .= '</tbody></table>';
      PDF::SetTitle('كل الصناديق');
      PDF::SetAuthor($by);
      // set some language dependent data:
      $lg = array();
      $lg['a_meta_charset'] = 'UTF-8';
      $lg['a_meta_dir'] = 'rtl';
      $lg['a_meta_language'] = 'ar';
      $lg['w_page'] = 'page';
      // make page horizontal
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

      // Ensure the directory exists before saving the file
      $directoryPath = storage_path('app/public/pdf/الصناديق' . '/' . date('Y-m-d'));
      if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0755, true);
      }

      // Save the file to the storage folder
      $filePath = $directoryPath . '/' . 'كشف كل الصناديق_' . date('Y-m-d-his') . '.pdf';
      PDF::Output($filePath, 'F');
      // Ensure the symbolic link exists for the storage folder
      if (!file_exists(public_path('storage'))) {
        symlink(storage_path('app/public'), public_path('storage'));
      }
      return response()->json(['status' => 'success', 'file' => asset('storage/pdf/' . $filePath)]);

    } else {
      // movements pdf
      $movements = Movement::with([
        'user:id,name',
        'box:id,name,currency_id',
        'box.currency:id,symbol'
      ])
        ->whereBetween('date_created', [$from, $to])
        ->where('box_id', $box_id)
        ->orderBy('id', 'DESC')
        ->get();

      $i = 1;
      $total = 0;
      $time = date('H:i:s');
      $date = date('Y-m-d');
      $by = Auth::user()->name;
      $company = config('app.company');

      $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل الحركات المالية</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
      $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="20%" bgcolor="#eee">التاريخ</th>
            <th width="20%" bgcolor="#eee">الحركة</th>
            <th width="10%" bgcolor="#eee">المبلغ</th>
            <th width="20%" bgcolor="#eee">الصندوق</th>
            <th width="10%" bgcolor="#eee">نوع الحركة</th>
            <th width="10%" bgcolor="#eee">بواسطة</th>
          </tr>
        </thead>
        <tbody>';
      foreach ($movements as $movement) {
        $type = '';
        if ($movement->type == 1) {
          $type = 'ايداع';
        } else {
          $type = 'سحب';
        }
        $table_content .= '<tr>
              <td width="10%">' . $i . '</td>
              <td width="20%">' . $movement->date_created . '</td>
              <td width="20%">' . $movement->from . '</td>
              <td width="10%">' . $movement->balance . ' ' . $movement->box->currency->symbol . '</td>
              <td width="20%">' . $movement->box->name . '</td>
              <td width="10%">' . $type . '</td>
              <td width="10%">' . $movement->user->name . '</td>
            </tr>';
        $total += $movement->balance;
        $i++;
      }

      $table_content .= '</tbody></table>';
      PDF::SetTitle('كل الحركات المالية');
      PDF::SetAuthor($by);
      // set some language dependent data:
      $lg = array();
      $lg['a_meta_charset'] = 'UTF-8';
      $lg['a_meta_dir'] = 'rtl';
      $lg['a_meta_language'] = 'ar';
      $lg['w_page'] = 'page';
      // make page horizontal
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

      // PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $total . '</td></tr></tbody></table>');
      // Ensure the directory exists before saving the file
      $directoryPath = storage_path('app/public/pdf/الحركات المالية' . '/' . date('Y-m-d'));
      // $directoryPath = '/media/ahmed/Downloads';
      if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0755, true);
      }

      // Save the file to the storage folder
      $filePath = $directoryPath . '/' . 'كشف الحركات المالية_' . date('Y-m-d-his') . '.pdf';
      PDF::Output($filePath, 'F');
      // dd($filePath);
      // Ensure the symbolic link exists for the storage folder
      if (!file_exists(public_path('storage'))) {
        symlink(storage_path('app/public'), public_path('storage'));
      }
      // PDF::Output('all_customers_' . date('ymdhis') . '.pdf', 'I');
      return response()->json(['status' => 'success', 'file' => asset('storage/pdf/' . $filePath)]);
    }
  }

  public function to_xlsx(Request $request)
  {

    $fileName = 'كشف صندوق_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/الصناديق' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new BoxExport(), 'xlsx/الصناديق' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
