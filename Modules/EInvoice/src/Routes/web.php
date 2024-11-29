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
use Workdo\EInvoice\Http\Controllers\EInvoiceController;

Route::middleware(['web','auth','verified'])->group(function () {
    Route::group(['middleware' => 'PlanModuleCheck:EInvoice'], function () {
        Route::prefix('einvoice')->group(function() {
            Route::get('/', [EInvoiceController::class,'index']);
            Route::get('/invoice/download/{id}',[EInvoiceController::class,'download'])->name('invoice.download');
            Route::post('/setting/store', [EInvoiceController::class,'setting'])->name('einvoice.setting.store')->middleware(['auth']);
        });
    });
});
