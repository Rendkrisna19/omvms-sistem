<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// Import Controller HR
use App\Http\Controllers\Api\Hr\DepartmentController;
use App\Http\Controllers\Api\Hr\EmployeeController;
use App\Http\Controllers\Api\Hr\UserController;

// --- AUTHENTICATION ROUTES ---
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ============================================================
    // GROUP 1: HR MANAGEMENT SYSTEM (Untuk Frontend Dashboard HR)
    // Prefix URL: /api/hr/departments, /api/hr/employees
    // ============================================================
    Route::prefix('hr')->name('hr.')->group(function () {
        
        // 1. Master Departemen
        Route::apiResource('departments', DepartmentController::class);
        
        // 2. Master Karyawan
        Route::apiResource('employees', EmployeeController::class);
        
        // 3. User Management (Akun Login)
        Route::apiResource('users', UserController::class);
        
        // Endpoint khusus Import/Export karyawan (Nanti)
        Route::post('employees/import', [EmployeeController::class, 'import']);
    });

    // ============================================================
    // GROUP 2: VOUCHER SYSTEM (Untuk Frontend Voucher & POS)
    // Prefix URL: /api/voucher/... (KITA SKIP DULU SESUAI REQUEST)
    // ============================================================
});