<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Kafeel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Exports\KafeelExport;
use App\Imports\KafeelImport;
use Maatwebsite\Excel\Facades\Excel;

class KafeelController extends Controller
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
    $kafeels = Kafeel::select('id', 'name', 'notes', 'phone', 'identity', 'status', 'user_id', 'created_at')
      ->with(['user:id,name'])
      ->orderBy('id', 'DESC')
      ->paginate($page);
    $pages = ceil(Kafeel::count() / $page);
    if ($request->ajax()) {
      $table = view('admin.kafeel.table', compact('kafeels'))->render();
      return response()->json(['table' => $table]);
    }
    return view('admin.kafeel.index', compact('kafeels', 'pages'));
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
      $xlsx_data = Excel::toArray(new KafeelImport, $file_attachment);

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
            $kafeel = Kafeel::where('name', $row[1])->first();
            if ($kafeel) {
              $kafeel->update([
                'name' => $row[1],
                'notes' => $row[2],
              ]);
            } else {
              $kafeel = new Kafeel();
              $kafeel->name = $row[1];
              $kafeel->notes = $row[2];
              $kafeel->user_id = $user_id;
              $kafeel->save();
            }
          }
        }

        // Commit the transaction
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'تم اضافة الكفيل بنجاح']);
      } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollback();
        // return dd($e->getMessage());  // For debugging
        // return response()->json(['status' => 'error', 'message' => 'حدث خطأ أثناء حفظ الكفيل']);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
      }
    }

    DB::beginTransaction();
    try {

      $kafeel = new Kafeel();
      $kafeel->name = $name;
      $kafeel->notes = $notes;
      $kafeel->user_id = $user_id;
      $kafeel->save();

      DB::commit();
      return response()->json(['status' => 'success', 'message' => 'تم اضافة الكفيل بنجاح']);
    } catch (\Exception $e) {
      DB::rollBack();
      // dd($e->getMessage());
      return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  // edit
  public function edit($id)
  {
    $kafeel = Kafeel::select('id', 'name', 'user_id', 'identity', 'phone', 'notes', 'status')
      ->where('id', $id)
      ->first();
    return view('admin.kafeel.edit', compact('kafeel'));
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

      kafeel::where('id', $id)->update([
        'name' => $name,
        'notes' => $notes,
        'identity' => $identity,
        'phone' => $phone,
        'status' => $status,
      ]);

      DB::commit();
      return redirect('/kafeels')->with('success', 'تم تحديث الكفيل بنجاح!');
    } catch (Exception $e) {
      DB::rollBack();
      return redirect('/kafeel/edit/' . $id)->with('error', $e->getMessage());
    }
  }

  // delete
  public function delete(Request $request)
  {
    // delete buy bill
    $kafeel = Kafeel::where('id', $request['id'])->first();
    if ($kafeel != null) {

      $kafeel->delete();
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'error']);
    }
  }

  // to pdf
  public function to_pdf(Request $request)
  {
    $from = date($request['from'] . ' 00:00:00');
    $to = date($request['to'] . ' 23:59:59');
    $kafeels = Kafeel::select('id', 'name', 'phone', 'notes', 'identity', 'status', 'created_at', 'user_id')
    ->with('user:id,name')
    ->whereRaw('created_at >= ? AND created_at <= ?', [$from, $to])
    ->orderBy('id', 'DESC')
    ->get();

    $i = 1;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل الكفلاء</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
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
    foreach ($kafeels as $kafeel) {
      $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="15%">' . $kafeel->created_at . '</td>
              <td width="15%">' . $kafeel->name . '</td>
              <td width="15%">' . $kafeel->identity . '</td>
              <td width="15%">' . $kafeel->phone . '</td>
              <td width="10%">' . $kafeel->user->name . '</td>
              <td width="10%">' . ($kafeel->status == 1 ? 'مستمر' : 'خلص') . '</td>
              <td width="15%">' . $kafeel->notes . '</td>
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
    $directoryPath = storage_path('app/public/pdf/الكفلاء' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }
    // Save the file to the storage folder
    $filePath = $directoryPath . '/كشف الكفلاء_' . date('Y-m-d-his') . '.pdf';
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
    $fileName = 'كشف الكفلاء_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/الكفلاء' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new KafeelExport(), 'xlsx/الكفلاء' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
