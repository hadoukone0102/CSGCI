<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

// Route pour traiter le paiement (POST)
Route::post('/process_payment', [PaymentController::class, 'index'])->name('process_payment');

// Routes supplémentaires pour CinetPay
Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
