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
  'register' => true,
  'reset' => false,
  'verify' => false,
]);

Route::get('/', function () {
  return redirect('/home');
});
Route::get('/home', [App\Http\Controllers\Admin\HomeController::class, 'index']);
Route::get('/search', [App\Http\Controllers\Admin\HomeController::class, 'search']);
Route::get('/settings', [App\Http\Controllers\Admin\HomeController::class, 'settings']);
Route::post('/user/update', [App\Http\Controllers\Admin\HomeController::class, 'update_user']);

// permission
Route::get('/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'index']);
Route::get('/permission/create', [App\Http\Controllers\Admin\PermissionController::class, 'index']);
Route::post('/permission/store', [App\Http\Controllers\Admin\PermissionController::class, 'store']);
Route::get('/permission/edit/{id}', [App\Http\Controllers\Admin\PermissionController::class, 'edit']);
Route::post('/permission/updateOne', [App\Http\Controllers\Admin\PermissionController::class, 'updateOne']);
Route::get('/permission/upgrade', [App\Http\Controllers\Admin\PermissionController::class, 'upgrade']);
Route::post('/permission/grant', [App\Http\Controllers\Admin\PermissionController::class, 'grant']);

// box
Route::get('/boxes', [App\Http\Controllers\Admin\BoxController::class, 'index']);
Route::post('/box/store', [App\Http\Controllers\Admin\BoxController::class, 'store']);
Route::get('/box/edit/{id}', [App\Http\Controllers\Admin\BoxController::class, 'edit']);
Route::post('/box/update', [App\Http\Controllers\Admin\BoxController::class, 'update']);
Route::post('/box/delete', [App\Http\Controllers\Admin\BoxController::class, 'destroy']);
Route::post('/box/convert', [App\Http\Controllers\Admin\BoxController::class, 'convert']);
Route::post('/box/to_pdf', [App\Http\Controllers\Admin\BoxController::class, 'to_pdf']);
Route::post('/box/to_xlsx', [App\Http\Controllers\Admin\BoxController::class, 'to_xlsx']);

// movement
Route::get('/movements', [App\Http\Controllers\Admin\MovementController::class, 'index']);
Route::post('/movement/to_pdf', [App\Http\Controllers\Admin\MovementController::class, 'to_pdf']);
Route::post('/movement/to_xlsx', [App\Http\Controllers\Admin\MovementController::class, 'to_xlsx']);

// currency
Route::group(['prefix' => '/currency'], function () {
  Route::get('/', [App\Http\Controllers\Admin\CurrencyController::class, 'index']);
  Route::post('/store', [App\Http\Controllers\Admin\CurrencyController::class, 'store']);
  Route::get('/edit/{id}', [App\Http\Controllers\Admin\CurrencyController::class, 'edit']);
  Route::post('/update', [App\Http\Controllers\Admin\CurrencyController::class, 'update']);
  Route::post('/to_pdf', [App\Http\Controllers\Admin\CurrencyController::class, 'to_pdf']);
  Route::post('/to_xlsx', [App\Http\Controllers\Admin\CurrencyController::class, 'to_xlsx']);
});

// import_ainiats
Route::get('/import_ainiats', [App\Http\Controllers\Admin\ImportAiniatController::class, 'index']);
Route::get('/import_ainiat/create', [App\Http\Controllers\Admin\ImportAiniatController::class, 'create']);
Route::post('/import_ainiat/store', [App\Http\Controllers\Admin\ImportAiniatController::class, 'store']);
Route::get('/import_ainiat/show', [App\Http\Controllers\Admin\ImportAiniatController::class, 'show']);
Route::get('/import_ainiat/edit/{id}', [App\Http\Controllers\Admin\ImportAiniatController::class, 'edit']);
Route::post('/import_ainiat/update/{id}', [App\Http\Controllers\Admin\ImportAiniatController::class, 'update']);
Route::post('/import_ainiat/delete', [App\Http\Controllers\Admin\ImportAiniatController::class, 'delete']);
Route::post('/import_ainiat/delete_product/{id}', [App\Http\Controllers\Admin\ImportAiniatController::class, 'delete_product']);
Route::post('/import_ainiat/to_pdf', [App\Http\Controllers\Admin\ImportAiniatController::class, 'to_pdf']);
Route::post('/import_ainiat/to_xlsx', [App\Http\Controllers\Admin\ImportAiniatController::class, 'to_xlsx']);

// customers
Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index']);
Route::post('/customer/store', [App\Http\Controllers\Admin\CustomerController::class, 'store']);
Route::get('/customer/edit/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'edit']);
Route::post('/customer/update', [App\Http\Controllers\Admin\CustomerController::class, 'update']);
Route::post('/customer/to_pdf', [App\Http\Controllers\Admin\CustomerController::class, 'to_pdf']);
Route::post('/customer/kashf_to_pdf', [App\Http\Controllers\Admin\CustomerController::class, 'kashf_to_pdf']);
Route::post('/customer/to_xlsx', [App\Http\Controllers\Admin\CustomerController::class, 'to_xlsx']);

// selectives
Route::get('/selectives', [App\Http\Controllers\Admin\SelectiveController::class, 'index']);
Route::post('/selective/store', [App\Http\Controllers\Admin\SelectiveController::class, 'store']);
Route::get('/selective/edit/{id}', [App\Http\Controllers\Admin\SelectiveController::class, 'edit']);
Route::post('/selective/update', [App\Http\Controllers\Admin\SelectiveController::class, 'update']);
Route::post('/selective/delete/{id}', [App\Http\Controllers\Admin\SelectiveController::class, 'delete']);
Route::post('/selective/to_pdf', [App\Http\Controllers\Admin\SelectiveController::class, 'to_pdf']);
Route::post('/selective/kashf_to_pdf', [App\Http\Controllers\Admin\SelectiveController::class, 'kashf_to_pdf']);
Route::post('/selective/to_xlsx', [App\Http\Controllers\Admin\SelectiveController::class, 'to_xlsx']);

// mosques
Route::get('/mosques', [App\Http\Controllers\Admin\MosqueController::class, 'index']);
Route::post('/mosque/store', [App\Http\Controllers\Admin\MosqueController::class, 'store']);
Route::get('/mosque/edit/{id}', [App\Http\Controllers\Admin\MosqueController::class, 'edit']);
Route::post('/mosque/update', [App\Http\Controllers\Admin\MosqueController::class, 'update']);
Route::post('/mosque/to_pdf', [App\Http\Controllers\Admin\MosqueController::class, 'to_pdf']);
Route::post('/mosque/to_xlsx', [App\Http\Controllers\Admin\MosqueController::class, 'to_xlsx']);

// expenses
Route::get('/expenses', [App\Http\Controllers\Admin\ExpensesController::class, 'index']);
Route::post('/expense/store', [App\Http\Controllers\Admin\ExpensesController::class, 'store']);
Route::post('/expense/to_pdf', [App\Http\Controllers\Admin\ExpensesController::class, 'to_pdf']);
Route::post('/expense/to_xlsx', [App\Http\Controllers\Admin\ExpensesController::class, 'to_xlsx']);

// products
Route::get('/product/create', [App\Http\Controllers\Admin\ProductController::class, 'create']);
Route::post('/product/store', [App\Http\Controllers\Admin\ProductController::class, 'store']);
Route::get('/product/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit']);
Route::post('/product/update', [App\Http\Controllers\Admin\ProductController::class, 'update']);
Route::post('/product/delete', [App\Http\Controllers\Admin\ProductController::class, 'delete']);
Route::post('/product/jard_to_pdf', [App\Http\Controllers\Admin\ProductController::class, 'jard_to_pdf']);
Route::post('/product/to_pdf', [App\Http\Controllers\Admin\ProductController::class, 'to_pdf']);
Route::post('/product/to_xlsx', [App\Http\Controllers\Admin\ProductController::class, 'to_xlsx']);
Route::post('/product/jard_to_xlsx', [App\Http\Controllers\Admin\ProductController::class, 'jard_to_xlsx']);
Route::get('/product/price/{id}', [App\Http\Controllers\Admin\ProductController::class, 'price']);

// providers
Route::get('/providers', [App\Http\Controllers\Admin\ProviderController::class, 'index']);
Route::post('/provider/store', [App\Http\Controllers\Admin\ProviderController::class, 'store']);
Route::post('/provider/to_pdf', [App\Http\Controllers\Admin\ProviderController::class, 'to_pdf']);
Route::post('/provider/kashf_to_pdf', [App\Http\Controllers\Admin\ProviderController::class, 'kashf_to_pdf']);

// salaries
Route::post('/salary/create', [App\Http\Controllers\Admin\SalaryController::class, 'create']);
Route::post('/salary/store', [App\Http\Controllers\Admin\SalaryController::class, 'store']);
Route::post('/salary/to_pdf', [App\Http\Controllers\Admin\SalaryController::class, 'to_pdf']);

// sanadat_dapd
Route::get('/sanadat_qapds', [App\Http\Controllers\Admin\SanadatQapdController::class, 'index']);
Route::post('/sanadat_qapd/store', [App\Http\Controllers\Admin\SanadatQapdController::class, 'store']);
Route::post('/sanadat_qapd/delete', [App\Http\Controllers\Admin\SanadatQapdController::class, 'delete']);
Route::post('/sanadat_qapd/to_pdf', [App\Http\Controllers\Admin\SanadatQapdController::class, 'to_pdf']);
Route::post('/sanadat_qapd/to_xlsx', [App\Http\Controllers\Admin\SanadatQapdController::class, 'to_xlsx']);

// sanadat_sarf
Route::get('/sanadat_sarfs', [App\Http\Controllers\Admin\SanadatSarfController::class, 'index']);
Route::post('/sanadat_sarf/store', [App\Http\Controllers\Admin\SanadatSarfController::class, 'store']);
Route::post('/sanadat_sarf/delete', [App\Http\Controllers\Admin\SanadatSarfController::class, 'delete']);
Route::post('/sanadat_sarf/to_pdf', [App\Http\Controllers\Admin\SanadatSarfController::class, 'to_pdf']);
Route::post('/sanadat_sarf/to_xlsx', [App\Http\Controllers\Admin\SanadatSarfController::class, 'to_xlsx']);

// export_ainiats
Route::get('/export_ainiats', [App\Http\Controllers\Admin\ExportAiniatController::class, 'index']);
Route::post('/export_ainiat/store', [App\Http\Controllers\Admin\ExportAiniatController::class, 'store']);
Route::get('/export_ainiat/edit/{id}', [App\Http\Controllers\Admin\ExportAiniatController::class, 'edit']);
Route::post('/export_ainiat/update/{id}', [App\Http\Controllers\Admin\ExportAiniatController::class, 'update']);
Route::post('/export_ainiat/delete', [App\Http\Controllers\Admin\ExportAiniatController::class, 'delete']);
Route::post('/export_ainiat/delete_product/{id}', [App\Http\Controllers\Admin\ExportAiniatController::class, 'delete_product']);
Route::post('/export_ainiat/to_pdf', [App\Http\Controllers\Admin\ExportAiniatController::class, 'to_pdf']);
Route::post('/export_ainiat/to_xlsx', [App\Http\Controllers\Admin\ExportAiniatController::class, 'to_xlsx']);

// workers
Route::get('/workers', [App\Http\Controllers\Admin\WorkerController::class, 'index']);
Route::post('/worker/store', [App\Http\Controllers\Admin\WorkerController::class, 'store']);
Route::post('/worker/to_pdf', [App\Http\Controllers\Admin\WorkerController::class, 'to_pdf']);
Route::post('/worker/kashf_to_pdf', [App\Http\Controllers\Admin\WorkerController::class, 'kashf_to_pdf']);

// kafeels
Route::get('/kafeels', [App\Http\Controllers\Admin\KafeelController::class, 'index']);
Route::post('/kafeel/store', [App\Http\Controllers\Admin\KafeelController::class, 'store']);
Route::get('/kafeel/edit/{id}', [App\Http\Controllers\Admin\KafeelController::class, 'edit']);
Route::post('/kafeel/update', [App\Http\Controllers\Admin\KafeelController::class, 'update']);
Route::post('/kafeel/to_pdf', [App\Http\Controllers\Admin\KafeelController::class, 'to_pdf']);
Route::post('/kafeel/to_xlsx', [App\Http\Controllers\Admin\KafeelController::class, 'to_xlsx']);
Route::post('/kafeel/kashf_to_pdf', [App\Http\Controllers\Admin\KafeelController::class, 'kashf_to_pdf']);

// orphans
Route::get('/orphans', [App\Http\Controllers\Admin\OrphanController::class, 'index']);
Route::post('/orphan/store', [App\Http\Controllers\Admin\OrphanController::class, 'store']);
Route::get('/orphan/edit/{id}', [App\Http\Controllers\Admin\OrphanController::class, 'edit']);
Route::post('/orphan/update', [App\Http\Controllers\Admin\OrphanController::class, 'update']);
Route::post('/orphan/to_pdf', [App\Http\Controllers\Admin\OrphanController::class, 'to_pdf']);
Route::post('/orphan/payment/to_pdf', [App\Http\Controllers\Admin\OrphanController::class, 'payment_to_pdf']);
Route::post('/orphan/to_xlsx', [App\Http\Controllers\Admin\OrphanController::class, 'to_xlsx']);
Route::post('/orphan/kashf_to_pdf', [App\Http\Controllers\Admin\OrphanController::class, 'kashf_to_pdf']);

// wasis
Route::get('/wasis', [App\Http\Controllers\Admin\WasiController::class, 'index']);
Route::post('/wasi/store', [App\Http\Controllers\Admin\WasiController::class, 'store']);
Route::get('/wasi/edit/{id}', [App\Http\Controllers\Admin\WasiController::class, 'edit']);
Route::post('/wasi/update', [App\Http\Controllers\Admin\WasiController::class, 'update']);
Route::post('/wasi/to_pdf', [App\Http\Controllers\Admin\WasiController::class, 'to_pdf']);
Route::post('/wasi/to_xlsx', [App\Http\Controllers\Admin\WasiController::class, 'to_xlsx']);
Route::post('/wasi/kashf_to_pdf', [App\Http\Controllers\Admin\WasiController::class, 'kashf_to_pdf']);

// sms
Route::get('/sms', [App\Http\Controllers\Admin\SmsController::class, 'index']);
Route::post('/sms/store', [App\Http\Controllers\Admin\SmsController::class, 'store']);
Route::get('/sms/edit/{id}', [App\Http\Controllers\Admin\SmsController::class, 'edit']);
Route::post('/sms/update', [App\Http\Controllers\Admin\SmsController::class, 'update']);
Route::get('/sms/show/{reciever}', [App\Http\Controllers\Admin\SmsController::class, 'show']);
Route::post('/sms/send', [App\Http\Controllers\Admin\SmsController::class, 'send']);
