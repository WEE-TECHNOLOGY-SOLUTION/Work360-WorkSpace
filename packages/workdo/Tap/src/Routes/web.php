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
use Workdo\Tap\Http\Controllers\TapController;

Route::group(['middleware' => ['web','auth','verified','PlanModuleCheck:Tap']], function () {
    Route::prefix('tap')->group(function() {
        Route::post('/setting/store', [TapController::class, 'setting'])->name('tap.setting.store');
    });
});

Route::middleware(['web'])->group(function ()
{
    Route::prefix('tap')->group(function() {
        Route::post('plan-pay-with-tap', [TapController::class,'planPayWithTap'])->name('plan.pay.with.tap')->middleware(['auth']);
        Route::any('plan-get-tap-status/{plan_id}', [TapController::class,'planGetTapStatus'])->name('plan.get.tap.status')->middleware(['auth']);
    });

    Route::post('/invoice-pay-with-tap', [TapController::class, 'invoicePayWithTap'])->name('invoice.pay.with.tap');
    Route::get('/invoice-tap/{invoice_id}/{amount}/{type}', [TapController::class, 'getInvoicePaymentStatus'])->name('invoice.tap');

    Route::post('course-pay-with-tap/{slug?}', [TapController::class,'coursePayWithTap'])->name('course.pay.with.tap');
    Route::get('get-payment-status/{slug}', [TapController::class, 'getCoursePaymentStatus'])->name('course.get.tap');

    Route::prefix('hotel/{slug}')->group(function() {
        Route::post('pay-with-tap', [TapController::class,'BookingPayWithTap'])->name('pay.with.tap');
        Route::get('{amount}/get-tap-status/{couponid}', [TapController::class, 'getBookingPaymentStatus'])->name('booking.tap');
    });
});
