<?php

namespace App\Http\Controllers\Admin;

use App\Exports\JardProductExport;
use App\Exports\ProductExport;
use PDF;
use Exception;

use App\Models\Product;
use App\Models\Quantity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Selective;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

  // auth
  public function __construct()
  {
    $this->middleware('auth');
  }

  // create
  public function create()
  {
    $modal = view('admin.product.create')->render();
    return response()->json(['status' => 'success', 'modal' => $modal]);
  }

  // store
  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      $product = new Product;
      $product->name = $request->name;
      $product->original_price = 0;
      $product->taqseet_price = $request->taqseet_price | 0;
      $product->quantity = 0;
      $product->original_quantity = 0;
      $product->export_ainiat_id = 0;
      $product->buy_bill_id = 0;
      $product->status = false;
      $product->type = $request->type;
      $product->save();

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
  public function edit(Request $request)
  {
    $product = Product::where('id', $request->id)->select('id', 'name', 'quantity', 'taqseet_price')->first();
    $modal = view('admin.product.edit', compact('product'))->render();
    return response()->json(['status' => 'success', 'modal' => $modal]);
  }

  // update
  public function update(Request $request)
  {

    $product_id = $request->product_id;
    $quantity = $request->quantity;

    DB::beginTransaction();
    try {

      $quantities = DB::select('SELECT id, product_id, quantity FROM `quantities` WHERE product_id = ? AND quantity = (SELECT MAX(quantity) FROM quantities WHERE product_id = ?)', [$product_id, $product_id]);

      $product = Product::where('id', $product_id)->first();

      DB::update('UPDATE quantities SET quantity = ? WHERE id = ?', [($quantity - $product->quantity) + $quantities[0]->quantity, $quantities[0]->id]);

      $product->update([
        'name' => $request->name,
        'quantity' => $quantity,
        'taqseet_price' => $request->taqseet_price | 0,
        'original_quantity' => ($quantity - $product->quantity) + $product->original_quantity,
      ]);

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      return $e->getMessage();
      return response()->json(['status' => 'error']);
    }
  }

  // delete product
  public function delete(Request $request)
  {
    DB::beginTransaction();
    try {

      Product::where('id', $request->id)->delete();

      DB::commit();
      return response()->json(['status' => 'success']);
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['status' => 'error']);
    }
  }

  // price
  public function price(Request $request, $id)
  {
    $quantities = Quantity::where('product_id', $id)->get();
    $price = view('admin.product.quantity', compact('quantities'))->render();
    return response()->json(['status' => 'success', 'price' => $price]);
  }

  // jard to pdf
  public function jard_to_pdf(Request $request)
  {
    $id = $request->id;
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');

    // $products = DB::select('SELECT sold_products.quantity, sold_products.profit, sold_products.sell_price, sold_products.total_price, sold_products.created_at, products.id, products.name, products.type, products.status, products.original_price, products.original_quantity, export_ainiats.number FROM sold_products INNER JOIN products ON sold_products.product_id = products.id INNER JOIN export_ainiats ON sold_products.export_ainiat_id = export_ainiats.id WHERE products.id = ? AND sold_products.created_at >= ? AND sold_products.created_at <= ? ORDER BY sold_products.id DESC', [$id, $from, $to]);

    $selectives = Selective::where('product_id', $id)
      ->with([
        'user:id,name',
        'customer:id,name',
        'product:id,name,status,type,export_ainiat_id',
        'product.export_ainiat:id,number,created_at',
      ])
      ->whereBetween('created_at', [$from, $to])
      ->orderBy('id', 'desc')
      ->get();
    dd($selectives->toArray());

    $i = 1;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل العينيات</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">#</th>
            <th width="20%" bgcolor="#eee">تاريخ الاستفادة</th>
            <th width="20%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="20%" bgcolor="#eee">اسم المستفيد</th>
            <th width="20%" bgcolor="#eee">بواسطة</th>
            <th width="10%" bgcolor="#eee">الحالة</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($selectives as $selective) {
      $status = 'مرشح';
      $number = '-';
      $product_status = 'خلص';
      if ($selective->product->status) {
        $product_status = 'موجود';
      }
      if ($selective->status == 1) {
        $status = 'مستفيد';
        $number = $selective->product->export_ainiat->number;
      }
      $table_content .= '<tr>
            <td width="10%">' . $i . '</td>
            <td width="20%">' . $selective->product->export_ainiat->created_at . '</td>
            <td width="20%">' . $number . '</td>
            <td width="20%">' . $selective->customer->name . '</td>
            <td width="20%">' . $selective->user->name . '</td>
            <td width="10%">' . $status . '<span>&#8362;&#160;</span></td>
          </tr>';
      $i++;
    }

    $table_content .= '</tbody></table>';
    PDF::SetTitle('جرد عينية');
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

    PDF::AddPage('P', 'A4');
    PDF::writeHTML($content);
    PDF::SetFont('freeserif', '', 11);

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center">
        <tbody>
        <tr>
            <td width="10%">#</td>
            <td width="30%">' . $selective->product->name . '</td>
            <td width="30%">' . $selective->product->type . '</td>
            <td width="30%" color="#fff" bgcolor="#003B36">' . $product_status . '</td>
        </tr>
        </tbody></table>');

    PDF::writeHTML($table_content);

    // PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center">
    //     <tbody>
    //     <tr>
    //         <td width="10%">#</td>
    //         <td width="60%">المجموع</td>
    //     </tr>
    //     </tbody></table>');

    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/العينيات' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/جرد عينية_' . $selectives[0]->product->name . '_' . date('Y-m-d-his') . '.pdf';
    PDF::Output($filePath, 'F');
    // Ensure the symbolic link exists for the storage folder
    if (!file_exists(public_path('storage'))) {
      symlink(storage_path('app/public'), public_path('storage'));
    }
    return response()->json(['status' => 'success']);
  }

  // to pdf
  public function to_pdf(Request $request)
  {
    $from = date($request->from . ' 00:00:00');
    $to = date($request->to . ' 23:59:59');
    $products = DB::select('SELECT id, name, quantity, original_quantity, created_at, status, type FROM products WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

    $i = 1;
    $total = 0;
    $available_total = 0;
    $time = date('H:i:s');
    $date = date('Y-m-d');
    $by = Auth::user()->name;

    $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h1 align="center">كشف كل العينيةات</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '&#160;&#160;من: ' . $from . ' - الى: ' . $to . '</p></br>';
    $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">#</th>
            <th width="30%" bgcolor="#eee">تاريخ الانشاء</th>
            <th width="30%" bgcolor="#eee">الاسم</th>
            <th width="10%" bgcolor="#eee">عدد الوحدات الاصلية</th>
            <th width="10%" bgcolor="#eee">عدد الوحدات المتوفرة</th>
            <th width="10%" bgcolor="#eee">الحالة</th>
          </tr>
        </thead>
        <tbody>';
    foreach ($products as $product) {
      $status = '';
      if ($product->status == 1) {
        $status = 'موجود';
      } else {
        $status = 'خلص';
      }
      $table_content .= '<tr>
              <td width="10%">' . $i . '</td>
              <td width="30%">' . $product->created_at . '</td>
              <td width="30%">' . $product->name . '</td>
              <td width="10%">' . $product->original_quantity . '</td>
              <td width="10%">' . $product->quantity . '</td>
              <td width="10%">' . $status . '</td>
            </tr>';
      $i++;
      $available_total += $product->quantity;
    }

    $table_content .= '</tbody></table>';
    PDF::SetTitle('كل العينيةات');
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

    PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="10%">' . $available_total . '</td></tr></tbody></table>');

    // Ensure the directory exists before saving the file
    $directoryPath = storage_path('app/public/pdf/العينيات' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the storage folder
    $filePath = $directoryPath . '/العينيات_' . date('Y-m-d-his') . '.pdf';
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

    $fileName = 'كشف العينيات_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/العينيات' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new ProductExport(), 'xlsx/العينيات/' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }

  // jard to xlsx
  public function jard_to_xlsx(Request $request)
  {
    $fileName = 'كشف عينية_' . date('Y-m-d_His') . '.xlsx';

    // Ensure the directory exists
    $directoryPath = public_path('storage/xlsx/العينيات' . '/' . date('Y-m-d'));
    if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0755, true);
    }

    // Save the file to the specified path
    Excel::store(new JardProductExport(), 'xlsx/العينيات/' . '/' . date('Y-m-d') . '/' . $fileName, 'public');

    // Return the file path for download
    return response()->json(['status' => 'success', 'file' => asset('storage/xlsx/' . $fileName)]);
  }
}
