<?php

use App\Http\Controllers\API\LeaveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\PerformanceReview\PerformanceReviewController;
use Illuminate\Support\Facades\Route;



    // Public Routes
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login',    [AuthController::class, 'login'])->name('login');

    // Protected Routes - Require JWT token
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/upload', [EmployeeController::class, 'uploadFiles']);

    // Attendance
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);

    // Leave
    Route::post('/leave/request', [LeaveController::class, 'requestLeave']);

    //Tasks
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/tasks/{id}/complete', [TaskController::class, 'markAsCompleted']);

    //PerformanceReviews
    Route::get('/performance-reviews', [PerformanceReviewController::class, 'index']);
    Route::post('/performance-reviews', [PerformanceReviewController::class, 'store']);
    Route::get('/performance-reviews/{id}', [PerformanceReviewController::class, 'show']);
    Route::put('/performance-reviews/{id}', [PerformanceReviewController::class, 'update']);
    Route::delete('/performance-reviews/{id}', [PerformanceReviewController::class, 'destroy']);
    Route::post('/performance-reviews/remind', [PerformanceReviewController::class, 'sendReminders']);


    //approve/reject of leave
   Route::middleware('role:admin')->group(function () {
        Route::post('/leave/{id}/approve', [LeaveController::class, 'approve']);
        Route::post('/leave/{id}/reject', [LeaveController::class, 'reject']);
       Route::post('/payroll', [PayrollController::class, 'store']);
    });
    Route::get('/payslips/download/{user_id}', [PayrollController::class, 'downloadPayslip']);
});
