<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use DB;
use PDF;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $product = new Product;
            $product->name = $request->name;
            $product->original_price = 0;
            $product->quantity = 0;
            $product->original_quantity = 0;
            $product->sell_bill_id = 0;
            $product->buy_bill_id = 0;
            $product->status = true;
            $product->type = $request->type;
            $product->save();

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error']);
        }
    }
    public function jard_to_pdf(Request $request)
    {
        $id = $request->id;
        $from = $request->from;
        $to = $request->to;
        $products = DB::select('SELECT sold_products.quantity, sold_products.sell_price, sold_products.total_price, products.name, products.type, products.status FROM sold_products INNER JOIN products ON sold_products.product_id = products.id WHERE sold_products.created_at >= :from AND sold_products.created_at <= :to AND products.id = :id ORDER BY sold_products.id DESC', ['id' => $id, 'from' => $from, 'to' => $to]);

        $i = 1; $total = 0; $total_soled = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = \Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف كل المنتجات</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%">الرقم</th>
            <th width="30%">الاسم</th>
            <th width="20%">سعر البيع</th>
            <th width="10%">الكمية</th>
            <th width="20%">السعر الكلي</th>
            <th width="10%">الحالة</th>
          </tr>
        </thead>
        <tbody>';
        foreach($products as $product) {
            $status = '';
            if ($product->status == 1) {
                $status = 'موجود';
            } else {
                $status = 'غير موجود';
            }
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="30%">'.$product->name.' - '.$product->type.'</td>
              <td width="20%">'.$product->sell_price.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$product->quantity.'</td>
              <td width="20%">'.$product->total_price.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$status.'</td>
            </tr>';
            $total += $product->quantity; $i++;
            $total_soled += $product->sell_price;
        }
        
        $table_content .= '</tbody></table>';
        PDF::SetTitle('جرد منتج');
        PDF::SetAuthor('اياد الهسي');
        // set some language dependent data:
        $lg = Array();
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="20%">0<span>&#8362;&#160;</span></td><td width="10%">'.$total.'</td><td width="20%">'.$total_soled.'<span>&#8362;&#160;</span></td></tr></tbody></table>');
        PDF::Output('all_products-'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
    public function to_pdf(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $products = DB::select('SELECT id, name, quantity, original_quantity, original_price, status, type FROM products WHERE created_at >= :from AND created_at <= :to ORDER BY id DESC', ['from' => $from, 'to' => $to]);

        $i = 1; $total = 0; $available_total = 0; $time = date('H:i:s'); $date = date('Y-m-d'); $by = \Auth::user()->name;
        $content = '<h4 align="center">بسم الله الرحمن الرحيم</h4><h3 align="center">شركة اياد الهسي للتجارة العامة</h3><h1 align="center">كشف كل المنتجات</h1></br><p align="right">التاريخ: '.$date.'&#160;&#160;الوقت: '.$time.'&#160;&#160;بواسطة: '.$by.'</p><p align="right">من: '.$from.' - الى: '.$to.'</p></br>';
        $table_content = '<table border="1" cellspacing="0" cellpadding="5" align="center">
        <thead>
          <tr>
            <th width="10%" bgcolor="#eee">الرقم</th>
            <th width="30%" bgcolor="#eee">الاسم</th>
            <th width="10%" bgcolor="#eee">سعر التكلفة</th>
            <th width="10%" bgcolor="#eee">عدد الوحدات الاصلية</th>
            <th width="10%" bgcolor="#eee">عدد الوحدات المتوفرة</th>
            <th width="20%" bgcolor="#eee">السعر الكلي</th>
            <th width="10%" bgcolor="#eee">الحالة</th>
          </tr>
        </thead>
        <tbody>';
        foreach($products as $product) {
            $status = '';
            if ($product->status == 1) {
                $status = 'موجود';
            } else {
                $status = 'غير موجود';
            }
            $table_content .= '<tr>
              <td width="10%">'.$i.'</td>
              <td width="30%">'.$product->name.'</td>
              <td width="10%">'.$product->original_price.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$product->original_quantity.'</td>
              <td width="10%">'.$product->quantity.'</td>
              <td width="20%">'.$product->quantity * $product->original_price.'<span>&#8362;&#160;</span></td>
              <td width="10%">'.$status.'</td>
            </tr>';
            $total += $product->quantity * $product->original_price; $i++;
            $available_total += $product->quantity;
        }
        
        $table_content .= '</tbody></table>';
        PDF::SetTitle('كل المنتجات');
        PDF::SetAuthor('اياد الهسي');
        // set some language dependent data:
        $lg = Array();
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

        PDF::writeHTML('<table border="1" cellspacing="0" cellpadding="5" align="center"><tbody><tr><td width="10%">#</td><td width="30%">المجموع</td><td width="10%">0<span>&#8362;&#160;</span></td><td width="10%">0</td><td width="10%">'.$available_total.'</td><td width="20%">'.$total.'<span>&#8362;&#160;</span></td></tr></tbody></table>');
        PDF::Output('all_products-'.date('ymdhis').'.pdf','I');
        return response()->json(['status' => 'success']);
    }
}
