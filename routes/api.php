<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\CertificateTypeController;
use App\Http\Controllers\Api\CertificateController;

// Public routes - Authentication
Route::post('login', [AuthController::class, 'login']);

// Protected routes - Require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Departments
    Route::apiResource('departments', DepartmentController::class);

    // Employees
    Route::apiResource('employees', EmployeeController::class);

    // Contracts
    Route::apiResource('contracts', ContractController::class);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{id}/assign', [ProjectController::class, 'assignEmployee']);
    Route::delete('projects/{id}/remove/{employeeId}', [ProjectController::class, 'removeEmployee']);

    // Certificate Types
    Route::apiResource('certificate-types', CertificateTypeController::class);

    // Certificates
    Route::apiResource('certificates', CertificateController::class);
    Route::get('certificates/employee/{id}', [CertificateController::class, 'byEmployee']);
    Route::get('certificates/type/{id}', [CertificateController::class, 'byType']);
    Route::get('certificates/expiring/list', [CertificateController::class, 'expiring']);
    Route::get('certificates/expired/list', [CertificateController::class, 'expired']);
});
