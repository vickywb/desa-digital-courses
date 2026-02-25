<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
    });

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes (semua role)
    Route::prefix('auth')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('logout', 'logout');
            Route::get('me', 'me');
        });

    // ✅ Admin only routes
    Route::middleware('role:admin')
        ->prefix('admin')
        ->group(function () {
        });

    // ✅ Head of Family routes
    Route::middleware('role:head_of_family')
        ->prefix('head-of-family')
        ->group(function () {
        });

    // ✅ Family Member routes
    Route::middleware('role:family_member')
        ->prefix('family-member')
        ->group(function () {
        });
});
