<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Billing\InvoiceController;


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



Route::get('/', function () {
    // this route is used by AWS ELB and New Relic (Health Check)
    return response()->json(['status' => true]);
})->name('healthCheck');

Route::prefix('webhook')->group(function () {
    Route::prefix('invoice')->group(function () {
        Route::post('change-status', [InvoiceController::class, 'changeStatus'])->name('invoice.changeStatus');
    });
});
