<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserCoursesController;
use App\Http\Controllers\EducationalHistoryController;

use App\Http\Controllers\RegistrationController;

use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\ProgramReportController;

use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;


use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::view('/conditions', 'conditions')->name('conditions');

// Auth: Login & Register
// ==========================
Route::get('/auth/phone', [AuthController::class, 'showPhoneForm'])->name('auth.phone');
Route::post('/auth/phone', [AuthController::class, 'requestOtp'])->name('auth.requestOtp');

Route::get('/auth/verify', [AuthController::class, 'showVerifyForm'])->name('auth.verifyForm');
Route::post('/auth/verify', [AuthController::class, 'verifyOtp'])->name('auth.verifyOtp');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/auth/login/request-otp', [AuthController::class, 'loginRequestOtp'])->name('auth.login.requestOtp');
Route::get('/auth/login/verify', [AuthController::class, 'showLoginVerifyForm'])->name('auth.login.verifyForm');
Route::post('/auth/login/verify', [AuthController::class, 'loginVerifyOtp'])->name('auth.login.verifyOtp');

Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/auth/register/request-otp', [AuthController::class, 'registerRequestOtp'])->name('auth.register.requestOtp');
Route::get('/auth/register/verify', [AuthController::class, 'showRegisterVerifyForm'])->name('auth.register.verifyForm');
Route::post('/auth/register/verify', [AuthController::class, 'registerVerifyOtp'])->name('auth.register.verifyOtp');
// ==========================

// Old 3-step wizard routes removed (replaced by dashboard onboarding)

//general Programs
Route::get('/programs', [ProgramController::class, 'archive'])->name('programs.archive');
Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');


//general Courses
Route::get('/courses', [CourseController::class, 'archive'])->name('courses.archive');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');



// User Dashboard routes (single canonical set)
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('index');

    // Profile
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    // Medical Record
    Route::get('/medical', [MedicalRecordController::class, 'show'])->name('medicalRecord.edit');
    Route::put('/medical', [MedicalRecordController::class, 'update'])->name('medicalRecord.update');

    // Educational History
    Route::get('/educational-histories', [EducationalHistoryController::class, 'index'])->name('educationalHistory.index');
    Route::post('/educational-histories', [EducationalHistoryController::class, 'store'])->name('educationalHistory.store');
    Route::put('/educational-histories/{id}', [EducationalHistoryController::class, 'update'])->name('educationalHistory.update');
    Route::delete('/educational-histories/{id}', [EducationalHistoryController::class, 'destroy'])->name('educationalHistory.destroy');

    // Payments & Settings
    Route::get('/my-payments', [PaymentController::class, 'UserIndex'])->name('payments.index');
    Route::post('/my-payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
});

Route::get('/api/programs/list', [PaymentController::class, 'getPrograms']);
Route::get('/api/courses/list', [PaymentController::class, 'getCourses']);

// Registratoins for Users:

//get the form of registrations
Route::get('registrations/program/{program}', [RegistrationController::class, 'createProgram'])->name('registrations.program.create');
Route::get('registrations/course/{course}', [RegistrationController::class, 'createCourse'])->name('registrations.course.create');

// post the form of regsitrations
Route::post('/registrations/program/{program}', [RegistrationController::class, 'ProgramStore'])->name('registration.program.store');
Route::post('/registrations/course/{course}', [RegistrationController::class, 'CourseStore'])->name('registration.course.store');



//Admin Dashboard routes:

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/users/export', [AdminUserController::class, 'export'])->name('admin.users.export');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/memberships/pending', [AdminUserController::class, 'pendingMemberships'])->name('admin.memberships.pending');
    Route::post('/users/{id}/approve', [AdminUserController::class, 'approveMembership'])->name('admin.users.approve');
    Route::post('/users/{id}/reject', [AdminUserController::class, 'rejectMembership'])->name('admin.users.reject');


    Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('admin.payments.export');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/payments/{id}', [AdminPaymentController::class, 'show'])->name('admin.payments.show');
    Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])->name('admin.payments.approve');
    Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])->name('admin.payments.reject');

    // Programs
    Route::get('/programs', [ProgramController::class, 'index'])->name('admin.programs.index');
    Route::get('/programs/create', [ProgramController::class, 'create'])->name('admin.programs.create');
    Route::post('/programs', [ProgramController::class, 'store'])->name('admin.programs.store');
    Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('admin.programs.show');
    Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('admin.programs.edit');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('admin.programs.update');
    Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('admin.programs.destroy');

    // Program Reports
    Route::get('/program-reports', [ProgramReportController::class, 'index'])->name('admin.program_reports.index');
    Route::get('/program-reports/create', [ProgramReportController::class, 'create'])->name('admin.program_reports.create');
    Route::post('/program-reports', [ProgramReportController::class, 'store'])->name('admin.program_reports.store');
    Route::get('/program-reports/{programReport}', [ProgramReportController::class, 'show'])->name('admin.program_reports.show');
    Route::get('/program-reports/{programReport}/edit', [ProgramReportController::class, 'edit'])->name('admin.program_reports.edit');
    Route::put('/program-reports/{programReport}', [ProgramReportController::class, 'update'])->name('admin.program_reports.update');
    Route::delete('/program-reports/{programReport}', [ProgramReportController::class, 'destroy'])->name('admin.program_reports.destroy');
});

// (Removed duplicate route blocks at file end)

