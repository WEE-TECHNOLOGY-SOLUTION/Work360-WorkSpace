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
use Workdo\Inventory\Http\Controllers\InventoryController;

Route::middleware(['web','auth','verified'])->group(function ()
{
    Route::group(['middleware' => 'PlanModuleCheck:Inventory'], function () {
        Route::prefix('inventory')->group(function () {
            Route::any('inventory/{feild_id}/{type}', [InventoryController::class,'show'])->name('inventory.view');
            Route::resource('inventory', InventoryController::class);
        });
    });
});
