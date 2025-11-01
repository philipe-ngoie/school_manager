<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Students
    Route::resource('students', App\Http\Controllers\StudentController::class);
    
    // Teachers
    Route::resource('teachers', App\Http\Controllers\TeacherController::class);
    
    // Classes
    Route::resource('classes', App\Http\Controllers\SchoolClassController::class);
    
    // Subjects
    Route::resource('subjects', App\Http\Controllers\SubjectController::class);
    
    // Enrollments
    Route::resource('enrollments', App\Http\Controllers\EnrollmentController::class);
    
    // Grades
    Route::resource('grades', App\Http\Controllers\GradeController::class);
    
    // Attendances
    Route::resource('attendances', App\Http\Controllers\AttendanceController::class);
});
