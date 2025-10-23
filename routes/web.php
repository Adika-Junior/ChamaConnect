<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Simple health endpoint used by Docker healthchecks
Route::get('/healthz', function () {
    $ok = true;
    $dbMsg = 'ok';
    try {
        // attempt a lightweight DB connection check
        if (app()->bound('db')) {
            \DB::connection()->getPdo();
        }
    } catch (\Exception $e) {
        $ok = false;
        $dbMsg = $e->getMessage();
    }

    return response()->json([
        'status' => $ok ? 'ok' : 'error',
        'db' => $dbMsg,
    ], $ok ? 200 : 503);
});

// Phase 1: Auth & Verification routes
Route::prefix('auth')->group(function () {
    Route::post('invite', [App\Http\Controllers\Auth\InviteController::class, 'invite'])->middleware('auth');
    Route::post('register/{token}', [App\Http\Controllers\Auth\RegistrationController::class, 'register']);
    Route::post('admin/approve/{user}', [App\Http\Controllers\AdminApprovalController::class, 'approve'])->middleware('auth');
    Route::post('admin/reject/{user}', [App\Http\Controllers\AdminApprovalController::class, 'reject'])->middleware('auth');
});
