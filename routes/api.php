<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::post('/password/reset', [ForgotPasswordController::class, 'reset']);
Route::post('/password/verify-code', [ForgotPasswordController::class, 'verifyCode']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::post('/wallet-send', [WalletController::class, 'send'])->name('wallet.send');

    Route::get('/my-transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('my.transactions');
});
