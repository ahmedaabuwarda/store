<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Wasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Exports\WasiExport;
use App\Imports\WasiImport;
use Maatwebsite\Excel\Facades\Excel;

class WasiController extends Controller
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
    $wasis = Wasi::select('id', 'name', 'identity', 'phone', 'notes', 'status', 'user_id', 'created_at')
      ->with(['user:id,name'])
      ->orderBy('id', 'DESC')
      ->paginate($page);
    $pages = $wasis->lastPage();
    if ($request->ajax()) {
      $table = view('admin.wasi.table', compact('wasis'))->render();
      return response()->json(['table' => $table]);
    }
    return view('admin.wasi.index', compact('wasis', 'pages'));
  }

  // store
  public function store(Request $request)
  {

    $name = $request['name'];
    $notes = $request['notes'];
    $user_id = Auth::user()->id;

    $file_attachment = $request->file('file_attachment');

    $xlsx_data = null;

    if ($file_attachment != null && $request->name == null) {
      // Read the xlsx file
      $xlsx_data = Excel::toArray(new WasiImport, $file_attachment);

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
            $wasi = Wasi::where('name', $row[1])->first();
            if ($wasi) {
              $wasi->update([
                'name' => $row[1],
                'notes' => $row[2],
              ]);
            } else {
              $wasi = new Wasi();
              $wasi->name = $row[1];
              $wasi->notes = $row[2];
              $wasi->user_id = $user_id;
              $wasi->save();
            }
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة الوصي بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        // return dd($e->getMessage());  // For debugging
        // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ الوصي']);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }
    }

    DB::beginTransaction();
    try {

      $wasi = new Wasi();
      $wasi->name = $name;
      $wasi->notes = $notes;
      $wasi->user_id = $user_id;
      $wasi->save();

      DB::commit();
      return response()->json(['status' => 'success', 'message' => 'تم اضافة الوصي بنجاح']);
    } catch (\Exception $e) {
      DB::rollBack();
      // dd($e->getMessage());
      return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  // edit
  public function edit($id)
  {
    $wasi = Wasi::select('id', 'name', 'user_id', 'identity', 'phone', 'notes', 'status')
      ->where('id', $id)
      ->first();
    return view('admin.wasi.edit', compact('wasi'));
  }

  // update
  public function update(Request $request)
  {

    $name = $request['name'];
    $notes = $request['notes'];
    $identity = $request['identity'];
    $phone = $request['phone'];
    $status = $request['status'];
    $id = $request['id'];

    DB::beginTransaction();
    try {

      Wasi::where('id', $id)->update([
        'name' => $name,
        'notes' => $notes,
        'identity' => $identity,
        'phone' => $phone,
        'status' => $status,
      ]);

      DB::commit();
      return redirect('/wasis')->with('success', 'تم تحديث الكفيل بنجاح!');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/wasi/edit/' . $id)->with('error', $e->getMessage());
    }
  }

  public function delete(Request $request)
  {
    // delete buy bill
    $wasi = Wasi::where('id', $request['id'])->first();
    if ($wasi != null) {

      $wasi->delete();
      return response()->json(['status' => 'success', 'message' => 'تم حذف الوصي بنجاح']);
    } else {
      return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حذف الوصي']);
    }
  }

  // to pdf
  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');
    $wasis = Wasi::select('id', 'name', 'phone', 'notes', 'identity', 'status', 'created_at', 'user_id')
      ->with('user:id,name')
      ->whereRaw('created_at >= ? AND created_at <= ?', [$from, $to])
      ->orderBy('id', 'DESC')
      ->get();

    $i = 1;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل الاوصياء</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>
              <th width="5%" bgcolor="#eee">#</th>
              <th width="15%" bgcolor="#eee">تاريخ الانشاء</th>
              <th width="15%" bgcolor="#eee">الاسم</th>
              <th width="15%" bgcolor="#eee">رقم الهوية</th>
              <th width="15%" bgcolor="#eee">رقم الهاتف</th>
              <th width="10%" bgcolor="#eee">بواسطة</th>
              <th width="10%" bgcolor="#eee">الحالة</th>
              <th width="15%" bgcolor="#eee">الملاحظات</th>
            </tr>
          </thead>
          <tbody>';
    foreach ($wasis as $wasi) {
      $table_content .= '<tr>
                <td width="5%">' . $i . '</td>
                <td width="15%">' . $wasi->created_at . '</td>
                <td width="15%">' . $wasi->name . '</td>
                <td width="15%">' . $wasi->identity . '</td>
                <td width="15%">' . $wasi->phone . '</td>
                <td width="10%">' . $wasi->user->name . '</td>
                <td width="10%">' . ($wasi->status == 1 ? 'مستمر' : 'خلص') . '</td>
                <td width="15%">' . $wasi->notes . '</td>
              </tr>';
      $i++;
    }
    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل الكفلاء');
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
    PDF::SetMargins(PDF_MARGIN_LEFT, /*PDF_MARGIN_TOP,*/ PDF_MARGIN_RIGHT);
    PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
    PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

    PDF::AddPage();
    PDF::writeHTML($content);
    PDF::SetFont('freeserif', '', 11);
    PDF::writeHTML($table_content);

    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/الاوصياء' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }
    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف الاوصياء_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    return response()->json(['status' => 'success']);
  }

  // to xlsx
  public function to_xlsx(Request $request)
  {
    // dd(date($request->from . ' 00:00:00'));
    $fileName = 'كشف الاوصياء_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/الاوصياء' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new WasiExport(), 'xlsx/الاوصياء' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
