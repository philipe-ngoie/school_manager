<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    SchoolController,
    StudentController,
    TeacherController,
    SchoolClassController,
    SubjectController,
    EnrollmentController,
    GradeController,
    AttendanceController,
    FeeTypeController,
    InvoiceController,
    PaymentController,
    ExpenseController,
    RefundController,
    ReportController
};

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

// Public routes (no authentication required)
Route::post('/payments/webhook/cinetpay', [PaymentController::class, 'webhookCinetPay']);
Route::post('/payments/webhook/stripe', [PaymentController::class, 'webhookStripe']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // School management
    Route::apiResource('schools', SchoolController::class);
    
    // Teachers
    Route::apiResource('teachers', TeacherController::class);
    
    // School Classes
    Route::apiResource('school-classes', SchoolClassController::class);
    
    // Students
    Route::apiResource('students', StudentController::class);
    
    // Subjects
    Route::apiResource('subjects', SubjectController::class);
    
    // Enrollments
    Route::apiResource('enrollments', EnrollmentController::class);
    
    // Grades
    Route::apiResource('grades', GradeController::class);
    
    // Attendance
    Route::apiResource('attendances', AttendanceController::class);
    
    // Fee Types
    Route::apiResource('fee-types', FeeTypeController::class);
    
    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf']);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'sendEmail']);
    Route::post('invoices/{invoice}/refund', [InvoiceController::class, 'requestRefund']);
    
    // Payments
    Route::apiResource('payments', PaymentController::class);
    Route::post('payments/initiate', [PaymentController::class, 'initiatePayment']);
    Route::post('payments/verify', [PaymentController::class, 'verifyPayment']);
    
    // Expenses
    Route::apiResource('expenses', ExpenseController::class);
    
    // Refunds
    Route::apiResource('refunds', RefundController::class);
    Route::post('refunds/{refund}/process', [RefundController::class, 'process']);
    
    // Reports
    Route::get('reports/financials', [ReportController::class, 'financials']);
    Route::get('reports/students', [ReportController::class, 'students']);
    Route::get('reports/attendance', [ReportController::class, 'attendance']);
});
