<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/', function () { return redirect('/home'); });
Route::get('/home', [App\Http\Controllers\Admin\HomeController::class, 'index']);
Route::get('/search', [App\Http\Controllers\Admin\HomeController::class, 'search']);
Route::post('/box/to_pdf', [App\Http\Controllers\Admin\HomeController::class, 'to_pdf']);
Route::post('/box/store', [App\Http\Controllers\Admin\HomeController::class, 'box_store']);

// products
Route::get('/product/create', [App\Http\Controllers\Admin\ProductController::class, 'create']);
Route::post('/product/store', [App\Http\Controllers\Admin\ProductController::class, 'store']);
Route::get('/product/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit']);
Route::post('/product/update', [App\Http\Controllers\Admin\ProductController::class, 'update']);
Route::post('/product/delete', [App\Http\Controllers\Admin\ProductController::class, 'delete']);
Route::post('/product/jard_to_pdf', [App\Http\Controllers\Admin\ProductController::class, 'jard_to_pdf']);
Route::post('/product/to_pdf', [App\Http\Controllers\Admin\ProductController::class, 'to_pdf']);

// providers
Route::get('/providers', [App\Http\Controllers\Admin\ProviderController::class, 'index']);
Route::post('/provider/store', [App\Http\Controllers\Admin\ProviderController::class, 'store']);
Route::post('/provider/to_pdf', [App\Http\Controllers\Admin\ProviderController::class, 'to_pdf']);
Route::post('/provider/kashf_to_pdf', [App\Http\Controllers\Admin\ProviderController::class, 'kashf_to_pdf']);

// customers
Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index']);
Route::post('/customer/store', [App\Http\Controllers\Admin\CustomerController::class, 'store']);
Route::post('/customer/to_pdf', [App\Http\Controllers\Admin\CustomerController::class, 'to_pdf']);
Route::post('/customer/kashf_to_pdf', [App\Http\Controllers\Admin\CustomerController::class, 'kashf_to_pdf']);

// sanadat_sarf
Route::get('/sanadat_sarfs', [App\Http\Controllers\Admin\SanadatSarfController::class, 'index']);
Route::post('/sanadat_sarf/store', [App\Http\Controllers\Admin\SanadatSarfController::class, 'store']);
Route::post('/sanadat_sarf/delete', [App\Http\Controllers\Admin\SanadatSarfController::class, 'delete']);
Route::post('/sanadat_sarf/to_pdf', [App\Http\Controllers\Admin\SanadatSarfController::class, 'to_pdf']);

// sanadat_dapd
Route::get('/sanadat_qapds', [App\Http\Controllers\Admin\SanadatQapdController::class, 'index']);
Route::post('/sanadat_qapd/store', [App\Http\Controllers\Admin\SanadatQapdController::class, 'store']);
Route::post('/sanadat_qapd/delete', [App\Http\Controllers\Admin\SanadatQapdController::class, 'delete']);
Route::post('/sanadat_qapd/to_pdf', [App\Http\Controllers\Admin\SanadatQapdController::class, 'to_pdf']);

// buy_bills
Route::get('/buy_bills', [App\Http\Controllers\Admin\BuyBillController::class, 'index']);
Route::get('/buy_bill/create', [App\Http\Controllers\Admin\BuyBillController::class, 'create']);
Route::post('/buy_bill/store', [App\Http\Controllers\Admin\BuyBillController::class, 'store']);
Route::get('/buy_bill/show', [App\Http\Controllers\Admin\BuyBillController::class, 'show']);
Route::get('/buy_bill/edit/{id}', [App\Http\Controllers\Admin\BuyBillController::class, 'edit']);
Route::post('/buy_bill/update/{id}', [App\Http\Controllers\Admin\BuyBillController::class, 'update']);
Route::post('/buy_bill/delete_product/{id}', [App\Http\Controllers\Admin\BuyBillController::class, 'delete_product']);
Route::post('/buy_bill/to_pdf', [App\Http\Controllers\Admin\BuyBillController::class, 'to_pdf']);

// sell_bills
Route::get('/sell_bills', [App\Http\Controllers\Admin\SellBillController::class, 'index']);
Route::get('/sell_bill/create', [App\Http\Controllers\Admin\SellBillController::class, 'create']);
Route::post('/sell_bill/store', [App\Http\Controllers\Admin\SellBillController::class, 'store']);
Route::get('/sell_bill/show', [App\Http\Controllers\Admin\SellBillController::class, 'show']);
Route::get('/sell_bill/edit/{id}', [App\Http\Controllers\Admin\SellBillController::class, 'edit']);
Route::post('/sell_bill/update/{id}', [App\Http\Controllers\Admin\SellBillController::class, 'update']);
Route::post('/sell_bill/delete_product/{id}', [App\Http\Controllers\Admin\SellBillController::class, 'delete_product']);
Route::post('/sell_bill/to_pdf', [App\Http\Controllers\Admin\SellBillController::class, 'to_pdf']);

Route::get('/daily_sells', [App\Http\Controllers\Admin\DailySellsController::class, 'index']);
Route::get('/daily_sell/create', [App\Http\Controllers\Admin\DailySellsController::class, 'create']);
Route::post('/daily_sell/store', [App\Http\Controllers\Admin\DailySellsController::class, 'store']);
Route::get('/daily_sell/show', [App\Http\Controllers\Admin\DailySellsController::class, 'show']);
Route::get('/daily_sell/edit/{id}', [App\Http\Controllers\Admin\DailySellsController::class, 'edit']);
Route::post('/daily_sell/update/{id}', [App\Http\Controllers\Admin\DailySellsController::class, 'update']);
Route::post('/daily_sell/delete_product/{id}', [App\Http\Controllers\Admin\DailySellsController::class, 'delete_product']);
Route::post('/daily_sell/to_pdf', [App\Http\Controllers\Admin\DailySellsController::class, 'to_pdf']);

// workers
Route::get('/workers', [App\Http\Controllers\Admin\WorkerController::class, 'index']);
Route::post('/worker/store', [App\Http\Controllers\Admin\WorkerController::class, 'store']);
Route::post('/worker/to_pdf', [App\Http\Controllers\Admin\WorkerController::class, 'to_pdf']);
Route::post('/worker/kashf_to_pdf', [App\Http\Controllers\Admin\WorkerController::class, 'kashf_to_pdf']);

// salaries
Route::post('/salary/create', [App\Http\Controllers\Admin\SalaryController::class, 'create']);
Route::post('/salary/store', [App\Http\Controllers\Admin\SalaryController::class, 'store']);
Route::post('/salary/to_pdf', [App\Http\Controllers\Admin\SalaryController::class, 'to_pdf']);

// discounts
Route::get('/discounts', [App\Http\Controllers\Admin\DiscountAndExpensesController::class, 'index']);
Route::post('/discount/store', [App\Http\Controllers\Admin\DiscountAndExpensesController::class, 'store']);
Route::post('/discount/to_pdf', [App\Http\Controllers\Admin\DiscountAndExpensesController::class, 'to_pdf']);

// permission
Route::get('/permission/create', [App\Http\Controllers\Admin\PermissionController::class, 'index']);
Route::get('/permission/update', [App\Http\Controllers\Admin\PermissionController::class, 'update']);