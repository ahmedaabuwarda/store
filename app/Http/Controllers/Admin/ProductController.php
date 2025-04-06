<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Exception;

use App\Models\Product;
use App\Models\Quantity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function create ()
    {
        $modal = view('admin.product.create')->render();
        return response()->json(['status' => 'success', 'modal' => $modal]);
    }

    public function store (Request $request)
    {
        DB::beginTransaction();
        try {
            $product = new Product;
            $product->name = $request->name;
            $product->original_price = 0;
            $product->taqseet_price = $request->taqseet_price | 0;
            $product->quantity = 0;
            $product->original_quantity = 0;
            $product->sell_bill_id = 0;
            $product->buy_bill_id = 0;
            $product->status = false;
            $product->type = $request->type;
            $product->save();

            // DB::statement('UPDATE box SET box.counter = CASE box.id
            //     WHEN 3 THEN (SELECT counter FROM box WHERE box.id = 3)+1
            //     ELSE box.counter
            //     END
            // WHERE box.id IN(3);');
            DB::statement('UPDATE box SET box.counter = CASE box.id
            WHEN 3 THEN (SELECT temp.counter FROM (SELECT counter FROM box WHERE box.id = 3) AS temp)+1
            ELSE box.counter
            END
            WHERE box.id IN(3);
           ');

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            DB::rollBack();
            // return response()->json(['status' => 'error']);
            // return dd($e->getMessage());
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }

    public function edit (Request $request)
    {
        $product = Product::where('id', $request->id)->select('id', 'name', 'quantity', 'taqseet_price')->first();
        $modal = view('admin.product.edit', compact('product'))->render();
        return response()->json(['status' => 'success', 'modal' => $modal]);
    }

    public function update (Request $request)
    {

        $product_id = $request->product_id;
        $quantity = $request->quantity;

        DB::beginTransaction();
        try {

            $quantities = DB::select('SELECT id, product_id, quantity FROM `quantities` WHERE product_id = ? AND quantity = (SELECT MAX(quantity) FROM quantities WHERE product_id = ?)' , [$product_id, $product_id]);

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
    public function delete (Request $request)
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

    public function price (Request $request, $id)
    {
        $quantities = Quantity::where('product_id', $id)->get();
        $price = view('admin.product.quantity', compact('quantities'))->render();
        return response()->json(['status' => 'success', 'price' => $price]);
    }

    public function jard_to_pdf (Request $request)
    {
        $id = $request->id;
        $from = date($request->from.' 00:00:00');
        $to = date($request->to.' 23:59:59');

        $products = DB::select('SELECT sold_products.quantity, sold_products.profit, sold_products.sell_price, sold_products.total_price, sold_products.created_at, products.id, products.name, products.type, products.status, products.original_price, products.original_quantity, sell_bills.number FROM sold_products INNER JOIN products ON sold_products.product_id = products.id INNER JOIN sell_bills ON sold_products.sell_bill_id = sell_bills.id WHERE products.id = ? AND sold_products.created_at >= ? AND sold_products.created_at <= ? ORDER BY sold_products.id DESC', [$id, $from, $to]);

        $i = 1;
        $total = 0;
        $total_profit = 0;
        $total_soled = 0;
        $time = date('H:i:s');
        $date = date('Y-m-d');
        $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف كل المنتجات</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="5%" bgcolor="#eee">#</th>
            <th width="15%" bgcolor="#eee">رقم الفاتورة</th>
            <th width="15%" bgcolor="#eee">سعر الشراء</th>
            <th width="20%" bgcolor="#eee">سعر البيع</th>
            <th width="10%" bgcolor="#eee">الكمية</th>
            <th width="20%" bgcolor="#eee">السعر الكلي</th>
            <th width="15%" bgcolor="#eee">المربح</th>
          </tr>
        </thead>
        <tbody>';
        foreach ($products as $product) {
            $status = '';
            if ($product->status == 1) {
                $status = 'موجود';
            } else {
                $status = 'غير موجود';
            }
            $table_content .= '<tr>
              <td width="5%">' . $i . '</td>
              <td width="15%">' . $product->number . '</td>
              <td width="15%">' . ($product->total_price - $product->profit) / ($product->quantity) . '<span>&#8362;&#160;</span></td>
              <td width="20%">' . $product->sell_price . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $product->quantity . '</td>
              <td width="20%">' . $product->total_price . '<span>&#8362;&#160;</span></td>
              <td width="15%">' . $product->profit . '<span>&#8362;&#160;</span></td>
            </tr>';
            $total += $product->quantity;
            $i++;
            $total_soled += $product->sell_price * $product->quantity;
            $total_profit += $product->profit;
        }

        $table_content .= '</tbody></table>';
        PDF::SetTitle('جرد منتج');
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

        PDF::AddPage('P', 'A4');
        PDF::writeHTML($content);
        PDF::SetFont('freeserif', '', 11);

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center">
        <tbody>
        <tr>
            <td width="10%">#</td>
            <td width="30%">'.$products[0]->name.'</td>
            <td width="30%">'.$products[0]->type.'</td>
            <td width="30%" color="#fff" bgcolor="#003B36">'.$status.'</td>
        </tr>
        </tbody></table>');

        PDF::writeHTML($table_content);

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center">
        <tbody>
        <tr>
            <td width="10%">#</td>
            <td width="60%">المجموع</td>
        </tr>
        <tr>
            <td width="10%">#</td>
            <td width="30%">المباع</td>
            <td width="10%">' . $total . '</td>
            <td width="20%" color="#fff" bgcolor="#DB2E39">' . $total_soled . '<span>&#8362;&#160;</span></td>
        </tr>
        <tr>
            <td width="10%">#</td>
            <td width="30%">المتوفر</td>
            <td width="10%">' . ($products[0]->original_quantity - $total) . '</td>
            <td width="20%" color="#fff" bgcolor="#003B36">' . ($products[0]->original_quantity - $total) *  $products[0]->original_price . '<span>&#8362;&#160;</span></td>
        </tr>
        <tr>
            <td width="10%">#</td>
            <td width="30%">المربح</td>
            <td width="30%" color="#000" bgcolor="#FFCA2C">' . $total_profit . '<span>&#8362;&#160;</span></td>
        </tr>
        </tbody></table>');
        PDF::Output('all_products-' . date('ymdhis') . '.pdf', 'I');
        return response()->json(['status' => 'success']);
    }

    public function to_pdf (Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $products = DB::select('SELECT id, name, quantity, original_quantity, original_price, status, type FROM products WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

        $i = 1;
        $total = 0;
        $available_total = 0;
        $time = date('H:i:s');
        $date = date('Y-m-d');
        $by = Auth::user()->name;
        $company = config('app.company');

        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">'.$company.'</h3><h1 align="center">كشف كل المنتجات</h1></br><p align="right">التاريخ: ' . $date . '&#160;&#160;الوقت: ' . $time . '&#160;&#160;بواسطة: ' . $by . '</p><p align="right">من: ' . $from . ' - الى: ' . $to . '</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">#</th>
            <th width="30%" bgcolor="#eee">الاسم</th>
            <th width="10%" bgcolor="#eee">سعر التكلفة</th>
            <th width="10%" bgcolor="#eee">عدد الوحدات الاصلية</th>
            <th width="10%" bgcolor="#eee">عدد الوحدات المتوفرة</th>
            <th width="20%" bgcolor="#eee">السعر الكلي</th>
            <th width="10%" bgcolor="#eee">الحالة</th>
          </tr>
        </thead>
        <tbody>';
        foreach ($products as $product) {
            $status = '';
            if ($product->status == 1) {
                $status = 'موجود';
            } else {
                $status = 'غير موجود';
            }
            $table_content .= '<tr>
              <td width="10%">' . $i . '</td>
              <td width="30%">' . $product->name . '</td>
              <td width="10%">' . $product->original_price . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $product->original_quantity . '</td>
              <td width="10%">' . $product->quantity . '</td>
              <td width="20%">' . $product->quantity * $product->original_price . '<span>&#8362;&#160;</span></td>
              <td width="10%">' . $status . '</td>
            </tr>';
            $total += $product->quantity * $product->original_price;
            $i++;
            $available_total += $product->quantity;
        }

        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل المنتجات');
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="10%">0<span>&#8362;&#160;</span></td><td width="10%">0</td><td width="10%">' . $available_total . '</td><td width="20%">' . $total . '<span>&#8362;&#160;</span></td></tr></tbody></table>');
        PDF::Output('all_products-' . date('ymdhis') . '.pdf', 'I');
        return response()->json(['status' => 'success']);
    }
}
