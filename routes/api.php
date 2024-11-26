<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\PaymentController;
// use App\Http\Controllers\Pgh_hiburanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/pgh_hiburan', [Pgh_hiburanController::class, 'index'])->name('penagihan.hiburan.data');
// Route::get('/pgh_hiburan/payment', [PaymentController::class, 'createVirtualAccount'])->name('payment.create');