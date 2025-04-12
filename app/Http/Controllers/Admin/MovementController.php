<?php

namespace App\Http\Controllers\Admin;

use PDF;

use App\Models\Movement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\MovementExport;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class MovementController extends Controller
{
  //
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $page = config('app.page');
    if ($request->ajax()) {
      $movements = Movement::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $table = view('admin.movement.table', compact('movements'))->render();
      return response()->json(['table' => $table]);
    } else {
      $movements = Movement::select('id', 'box_id', 'user_id', 'balance', 'date_created', 'type', 'from')->with('box:id,name,currency_id')->with('box.currency:id,symbol')->with('user:id,name')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Movement::count() / $page);
      return view('admin.movement.index', compact('movements', 'pages'));
    }
  }

  public function to_pdf(Request $request)
  {
    $from = date($request->from . ' H:i:s');
    $to = date($request->to . ' H:i:s');
    // $movements = DB::select('SELECT name, balance, notes, status FROM customers WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);
    $movements = Movement::with([
      'user:id,name',
      'box:id,name,currency_id',
      'box.currency:id,symbol'
    ])
      ->whereBetween('date_created', [$from, $to])
      ->orderBy('id', 'DESC')
      ->get();
    // dd($movements);
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
      $balance = '';
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

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">' . $total . '</td></tr></tbody></table>');
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

  public function to_xlsx(Request $request)
  {

    $fileName = 'كشف الحركات المالية_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/الحركات المالية/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new MovementExport(), 'xlsx/الحركات المالية/'  . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
