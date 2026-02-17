<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\InstructorController;
use App\Http\Controllers\Superadmin\MessageController as SuperadminMessageController;
use App\Http\Controllers\Instructor\MessageController as InstructorMessageController;
use App\Http\Controllers\Superadmin\DocumentController as SuperadminDocumentController;
use App\Http\Controllers\Instructor\DocumentController as InstructorDocumentController;
use App\Http\Controllers\Instructor\ProfileController as InstructorProfileController;
use App\Http\Controllers\Auth\SuperadminAuthenticatedSessionController;
use App\Http\Controllers\Auth\InstructorAuthenticatedSessionController;
use App\Http\Controllers\Superadmin\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('instructor.login');
});

Route::get('/login', function () {
    if (auth('superadmin')->check()) {
        return redirect()->route('superadmin.dashboard');
    }
    if (auth('instructor')->check()) {
        return redirect()->route('instructor.dashboard');
    }
    return redirect()->route('instructor.login');
})->name('login');

// Portal login pages (two separate UIs)
Route::view('/superadmin/login', 'auth.login-superadmin')
    ->middleware('guest:superadmin')
    ->name('superadmin.login');

Route::view('/instructor/login', 'auth.login-instructor')
    ->middleware('guest:instructor')
    ->name('instructor.login');

// Superadmin login/logout (guard: superadmin)
Route::post('/superadmin/login', [SuperadminAuthenticatedSessionController::class, 'store'])
    ->middleware('guest:superadmin')
    ->name('superadmin.login.store');

Route::post('/superadmin/logout', [SuperadminAuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:superadmin')
    ->name('superadmin.logout');

// Instructor login/logout (guard: instructor)
Route::post('/instructor/login', [InstructorAuthenticatedSessionController::class, 'store'])
    ->middleware('guest:instructor')
    ->name('instructor.login.store');

Route::post('/instructor/logout', [InstructorAuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:instructor')
    ->name('instructor.logout');

// If anyone hits /dashboard (Breeze default), send them to correct portal dashboard
Route::get('/dashboard', function () {
    if (auth('superadmin')->check()) {
        return redirect()->route('superadmin.dashboard');
    }

    if (auth('instructor')->check()) {
        return redirect()->route('instructor.dashboard');
    }

    return redirect()->route('login');
})->name('dashboard');

// =========================
// Superadmin routes
// =========================
Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth:superadmin', 'role:superadmin'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('superadmin.dashboard');
        })->name('dashboard');

        // Instructors
        Route::get('/instructors', [InstructorController::class, 'index'])->name('instructors.index');
        Route::get('/instructors/create', [InstructorController::class, 'create'])->name('instructors.create');
        Route::post('/instructors', [InstructorController::class, 'store'])->name('instructors.store');
        Route::post('/instructors/{user}/resend-verification', [InstructorController::class, 'resendVerification'])
            ->name('instructors.resendVerification');
        Route::patch('/instructors/{user}/toggle-status', [InstructorController::class, 'toggleStatus'])
            ->name('instructors.toggleStatus');
        Route::get('/instructors/{user}/profile', [InstructorController::class, 'editProfile'])
        ->name('instructors.profile.edit');
        Route::patch('/instructors/{user}/profile', [InstructorController::class, 'updateProfile'])
            ->name('instructors.profile.update');
        Route::get('/instructors/{user}/edit', [InstructorController::class, 'edit'])
            ->name('instructors.edit');
        Route::patch('/instructors/{user}', [InstructorController::class, 'update'])
            ->name('instructors.update');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Chat-style Messages (no conflicts)
        Route::get('/messages', [SuperadminMessageController::class, 'threads'])->name('messages.threads');
        Route::get('/messages/chat/{instructor}', [SuperadminMessageController::class, 'chat'])->name('messages.chat');
        Route::post('/messages/chat/{instructor}', [SuperadminMessageController::class, 'send'])->name('messages.send');

        Route::get('/documents/{document}/download', [SuperadminDocumentController::class, 'download'])
        ->name('documents.download');
        Route::get('/documents', [SuperadminDocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [SuperadminDocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/preview', [SuperadminDocumentController::class, 'preview'])
        ->name('documents.preview');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
        Route::delete('/documents/{document}', [SuperadminDocumentController::class, 'destroy'])
        ->name('documents.destroy');
        Route::delete('/instructors/{user}', [InstructorController::class, 'destroy'])
        ->name('instructors.destroy');
    });

// =========================
// Instructor routes
// =========================
Route::prefix('instructor')
    ->name('instructor.')
    ->middleware(['auth:instructor', 'role:instructor', 'instructor.verified'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('instructor.dashboard');
        })->name('dashboard');

        Route::get('/profile', [InstructorProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [InstructorProfileController::class, 'update'])->name('profile.update');

        // Chat-style Messages (single superadmin)
        Route::get('/messages', [InstructorMessageController::class, 'chat'])->name('messages.chat');
        Route::post('/messages', [InstructorMessageController::class, 'send'])->name('messages.send');

        Route::get('/documents/{document}/download', [InstructorDocumentController::class, 'download'])
        ->name('documents.download');
        Route::get('/documents', [InstructorDocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [InstructorDocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/preview', [InstructorDocumentController::class, 'preview'])
        ->name('documents.preview');
        Route::delete('/documents/{document}', [InstructorDocumentController::class, 'destroy'])
        ->name('documents.destroy');
    });
    

require __DIR__ . '/auth.php';
