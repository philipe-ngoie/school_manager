<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('students', App\Http\Controllers\Api\StudentController::class);
    Route::apiResource('teachers', App\Http\Controllers\Api\TeacherController::class);
    Route::apiResource('classes', App\Http\Controllers\Api\SchoolClassController::class);
    Route::apiResource('subjects', App\Http\Controllers\Api\SubjectController::class);
    Route::apiResource('enrollments', App\Http\Controllers\Api\EnrollmentController::class);
    Route::apiResource('grades', App\Http\Controllers\Api\GradeController::class);
    Route::apiResource('attendances', App\Http\Controllers\Api\AttendanceController::class);
});
