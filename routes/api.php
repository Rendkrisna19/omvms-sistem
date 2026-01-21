<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Hr\DepartmentController;
use App\Http\Controllers\Api\Hr\EmployeeController;
use App\Http\Controllers\Api\Hr\UserController;

// ============================================================
// AUTHENTICATION (PUBLIC)
// ============================================================

// Login API (TANPA CSRF, TANPA COOKIE)
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // optional: cegah brute force


// ============================================================
// AUTHENTICATED ROUTES (SANCTUM BEARER TOKEN)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- AUTH ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ============================================================
    // GROUP 1: HR MANAGEMENT SYSTEM
    // Prefix: /api/hr/*
    // ============================================================
    Route::prefix('hr')->name('hr.')->group(function () {

        // --- MASTER DATA ---
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('users', UserController::class);

        // --- CUSTOM ACTION (HARUS DI ATAS jika bukan REST standar) ---
        Route::post('employees/import', [EmployeeController::class, 'import'])
            ->name('employees.import');
    });

    // ============================================================
    // GROUP 2: VOUCHER SYSTEM (SKIP)
    // ============================================================
});
