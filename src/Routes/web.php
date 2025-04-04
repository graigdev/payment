<?php

use Illuminate\Support\Facades\Route;
use GraigDev\Payment\Http\Controllers\WalletController;
use GraigDev\Payment\Http\Controllers\PaymentCodeController;
use GraigDev\Payment\Http\Controllers\TransactionController;

Route::middleware(['web', 'auth'])->prefix('payment')->name('payment.')->group(function () {
    // Wallet routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    
    // Payment code routes
    Route::get('/codes', [PaymentCodeController::class, 'index'])->name('codes.index');
    Route::post('/codes/generate', [PaymentCodeController::class, 'generate'])->name('codes.generate');
    Route::post('/codes/redeem', [PaymentCodeController::class, 'redeem'])->name('codes.redeem');
    
    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
}); 