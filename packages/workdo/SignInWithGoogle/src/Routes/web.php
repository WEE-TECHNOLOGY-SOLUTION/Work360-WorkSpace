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
use Workdo\SignInWithGoogle\Http\Controllers\SignInWIthGoogleController;

Route::middleware(['web','auth','verified'])->group(function ()
{
    Route::group(['middleware' => 'PlanModuleCheck:SignInWithGoogle'], function () {
        Route::post('googlesignin-setting', [SignInWIthGoogleController::class, 'setting'])->name('googlesignin.setting');
    });
});

Route::middleware(['web'])->group(function ()
{
    Route::get('login-google', [SignInWIthGoogleController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('auth/google/callback/', [SignInWIthGoogleController::class, 'GoogleCallback'])->name('google.callback');
});
