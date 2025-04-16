<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MosqueExport;
use Exception;
use PDF;

use App\Models\Mosque;

use App\Http\Controllers\Controller;
use App\Imports\MosqueImport;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MosqueController extends Controller
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
    $mosques = Mosque::select('id', 'name', 'notes', 'created_at')->orderBy('id', 'DESC')->paginate($page);

    if ($request->ajax()) {
      $table = view('admin.mosque.table', compact('mosques'))->render();
      return response()->json(['table' => $table]);
    } else {
      // $mosques = Mosque::select('id', 'name', 'notes', 'created_at')->orderBy('id', 'DESC')->paginate($page);
      $pages = ceil(Mosque::count() / $page);
      return view('admin.mosque.index', compact('mosques', 'pages'));
    }
  }

  // store
  public function store(Request $request)
  {
    $file_attachment = $request->file('file_attachment');

    $xlsx_data = null;

    if ($file_attachment != null && $request->name == null) {
      // Read the xlsx file
      $xlsx_data = Excel::toArray(new MosqueImport, $file_attachment);

      // Assuming we have only one sheet in the Excel file, so we'll take the first sheet
      $sheetData = $xlsx_data[0];

      // Start transaction
      DB::beginTransaction();
      try {
        // Loop through the rows, skip the first row (headers)
        for ($i = 1; $i < count($sheetData); $i++) {
          $row = $sheetData[$i];
          // Ensure the row is valid (not empty)
          if ($row[0] != null) {
            $mosque = Mosque::where('name', $row[1])->first();
            if ($mosque) {
              $mosque->update([
                'name' => $row[1],
                'notes' => $row[2],
              ]);
            } else {
              $currency = new Mosque();
              $currency->name = $row[1];
              $currency->notes = $row[2];
              $currency->save();
            }
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة المسجد بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        // return dd($e->getMessage());  // For debugging
        // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ المستفيدون']);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }
    }
    DB::beginTransaction();
    try {
      $currency = new Mosque();
      $currency->name = $request->name;
      $currency->notes = $request->notes;
      $currency->save();

      DB::commit();
      return response()->json(['status' => 'success', 'message' => 'تم اضافة المسجد بنجاح!']);
    } catch (Exception $e) {
      DB::rollBack();
      // return response()->json(['status' => 'error']);
      // return dd($e->getMessage());
      return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  // edit
  public function edit($id)
  {
    $mosque = Mosque::where('id', $id)->select('id', 'name', 'notes')->first();
    return view('admin.mosque.edit', compact('mosque'));
  }

  // update
  public function update(Request $request)
  {
    $id = $request->id;
    $name = $request->name;
    $notes = $request->notes;

    DB::beginTransaction();
    try {

      $mosque = Mosque::where('id', $id)->first();
      $mosque->update([
        'name' => $name,
        'notes' => $notes,
      ]);

      DB::commit();
      return redirect('/mosques')->with('success', 'تم تحديث المسجد بنجاح');
    } catch (Exception $e) {
      DB::rollBack();
      // return $e->getMessage();
      // return response()->json(['status' => 'error']);
      return redirect('/mosques')->with('error', 'حدث خطأ أثناء تحديث المسجد');
    }
  }

  // to_pdf
  public function to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');

    $mosques = Mosque::select('id', 'name', 'notes', 'created_at')
      ->whereBetween('created_at', [$from, $to])
      ->orderBy('id', 'DESC')
      ->get();

    $i = 1;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل المساجد</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="25%" bgcolor="#eee">الرقم</th>
            <th width="25%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="25%" bgcolor="#eee">الاسم</th>
            <th width="25%" bgcolor="#eee">ملاحظات</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($mosques as $mosque) {
      $table_content .= '<tr>
              <td width="25%">' . $i . '</td>
              <td width="25%">' . $mosque->created_at . '</td>
              <td width="25%">' . $mosque->name . '</td>
              <td width="25%">' . $mosque->notes . '</td>
            </tr>';
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل المساجد');
    PDF::SetAuthor($by);
    // set some language dependent data:
    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $lg['w_page'] = 'page';
    // PDF::SetPageOrientation('L', 'P');
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
    $directoryPath = storage_path('app/public/pdf/المساجد' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف المساجد' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // dd($filePath);
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    // PDF::Output('all_customers_' . date('ymdhis') . '.pdf', 'I');
    return response()->json(['status' => 'success']);
  }

  // to_xlsx
  public function to_xlsx()
  {
    // dd(date($request->from . ' 00:00:00'));
    $fileName = 'كشف المساجد_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/المساجد' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new MosqueExport(), 'xlsx/المساجد' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
