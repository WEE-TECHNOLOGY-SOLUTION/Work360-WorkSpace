<?php

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



use Illuminate\Support\Facades\Route;
use  Workdo\PayHere\Http\Controllers\PayHereController;
use  Workdo\PayHere\Http\Controllers\CallbackController;

Route::group(['middleware' => 'web'], function () {
Route::group(['middleware' => 'PlanModuleCheck:PayHere'], function () {


            Route::post('payhere/setting/store', [PayHereController::class,'setting'])->name('payhere.setting.store')->middleware(['auth']);
});

Route::post('/plan/company/payhere', [PayHereController::class, 'planPayWithPayHere'])->name('plan.pay.with.payhere')->middleware('auth');
Route::any('plan-get-payhere-status/{plan_id}', [PayHereController::class, 'planGetPayHereStatus'])->name('plan.get.payhere.status')->middleware('auth');

Route::post('/invoice-pay-with/payhere',[PayHereController::class,'invoicePayWithPayHere'])->name('invoice.pay.with.payhere');
Route::any('/invoice/payhere/{invoice_id}/{amount}/{type}',[PayHereController::class,'getInvoicePaymentStatus'])->name('invoice.payhere');


Route::post("payhere/callback/{type}",[CallbackController::class,'handle'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
    ->name('payhere.callback');
});

