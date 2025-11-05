<?php

use Illuminate\Support\Facades\Route;

// Public webhook endpoint for M-Pesa callbacks
Route::post('/webhooks/mpesa/callback', [\App\Http\Controllers\Payments\MpesaWebhookController::class, 'handle'])
    ->name('webhooks.mpesa.callback');


