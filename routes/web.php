<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\EnrolledStudentController;
use App\Http\Controllers\Auth\PortalAuthController;
use App\Http\Middleware\PortalAuth;
use App\Http\Middleware\AdminOnly;

/*
|--------------------------------------------------------------------------
| Landing / Marketing
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('index'))->name('home');

/*
|--------------------------------------------------------------------------
| Auth (Login / Create Account)
|--------------------------------------------------------------------------
*/
Route::get('/login', [PortalAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [PortalAuthController::class, 'login']);

Route::get('/create-account', [PortalAuthController::class, 'showRegister'])->name('create-account');
Route::post('/create-account', [PortalAuthController::class, 'register']);
Route::get('/api/check-student', [PortalAuthController::class, 'checkStudent'])->name('auth.check-student');

Route::post('/logout', [PortalAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Student ID Registration (requires login)
|--------------------------------------------------------------------------
*/
Route::get('/register', fn () => view('forms'))
    ->middleware(PortalAuth::class)
    ->name('students.register');

// Compatibility alias for legacy redirect in controller
Route::get('/register-old', function () {
    return redirect()->route('students.register');
})->name('register');

Route::post('/register', [StudentController::class, 'submit'])
    ->middleware(PortalAuth::class)
    ->name('students.submit');

/*
|--------------------------------------------------------------------------
| Admin Dashboard + Student APIs (admin only)
|--------------------------------------------------------------------------
*/
Route::get('/admin', fn () => view('admin'))
    ->middleware([PortalAuth::class, AdminOnly::class])
    ->name('admin.dashboard');

Route::prefix('admin/students')
    ->middleware([PortalAuth::class, AdminOnly::class])
    ->group(function () {
        // Tables (return blade partial rows)
        Route::get('/pending',  [StudentController::class, 'pending'])->name('students.pending');
        Route::get('/approved', [StudentController::class, 'approved'])->name('students.approved');

        // Preview returns HTML for the modal (GET ?id=<pk or student_number>)
        Route::get('/preview', [StudentController::class, 'preview'])->name('students.preview');

        // Mutations
        Route::post('/approve', [StudentController::class, 'approve'])->name('students.approve');
        Route::post('/decline', [StudentController::class, 'decline'])->name('students.decline');
        Route::post('/refresh-photo', [StudentController::class, 'refreshPhoto'])->name('students.refreshPhoto');

        // (Optional) one endpoint that sets any status you support
        Route::post('/update-status', [StudentController::class, 'updateStatus'])->name('students.updateStatus');

        // Print-ready PDF of multiple IDs
        Route::get('/print', [StudentController::class, 'print'])
            ->name('students.print');
    });

Route::prefix('admin/enrolled-students')
    ->middleware([PortalAuth::class, AdminOnly::class])
    ->group(function () {
        Route::get('/count', [EnrolledStudentController::class, 'count'])->name('enrolled.count');
        Route::post('/import', [EnrolledStudentController::class, 'import'])->name('enrolled.import');
    });

Route::prefix('admin/faculty')
    ->middleware([PortalAuth::class, AdminOnly::class])
    ->group(function () {
        Route::get('/list', [FacultyController::class, 'list'])->name('faculty.list');
        Route::post('/', [FacultyController::class, 'store'])->name('faculty.store');
        Route::get('/preview', [FacultyController::class, 'preview'])->name('faculty.preview');
        Route::get('/print', [FacultyController::class, 'print'])->name('faculty.print');
        Route::post('/delete', [FacultyController::class, 'destroy'])->name('faculty.delete');
    });
