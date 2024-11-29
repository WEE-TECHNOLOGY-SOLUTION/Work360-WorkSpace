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
use Workdo\Mollie\Http\Controllers\MollieController;

Route::group(['middleware' => 'web', 'verified','PlanModuleCheck:Mollie'], function () {
    Route::post('/setting/mollie_store', [MollieController::class, 'setting'])->name('mollie.company_setting.store')->middleware(['auth']);
});
Route::group(['middleware' => 'web'], function () {
    Route::post('/plan/mollie/payment', [MollieController::class, 'planPayWithMollie'])->name('plan.pay.with.mollie')->middleware(['auth']);
    Route::get('/plan/mollie/{plan}', [MollieController::class, 'planGetMollieStatus'])->name('plan.get.mollie.status')->middleware(['auth']);
    Route::post('/invoice.pay.with.mollie', [MollieController::class, 'invoicePayWithmollie'])->name('invoice.pay.with.mollie');
    Route::get('/invoice/mollie/{invoice}/{amount}/{type}', [MollieController::class, 'getInvoicePaymentStatus'])->name('invoice.mollie');
    Route::prefix('hotel/{slug}')->group(function () {
        Route::post('/booking-pay-with-mollie', [MollieController::class, 'bookingPayWithMollie'])->name('booking.pay.with.mollie');
        Route::get('/booking/mollie', [MollieController::class, 'getBookingPaymentStatus'])->name('booking.mollie');
    });
    Route::post('{slug}/course-pay-with-mollie', [MollieController::class, 'coursePayWithmollie'])->name('course.pay.with.mollie');
    Route::get('{slug}/{order_id}/course/mollie', [MollieController::class, 'getCoursePaymentStatus'])->name('course.mollie');
});
