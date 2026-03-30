<?php

use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Account Settings
    Route::get('settings', [AccountController::class, 'index'])->name('settings');
    Route::patch('settings/password', [AccountController::class, 'updatePassword'])->name('settings.password');

    // Student Routes
    Route::middleware('can:isStudent')->group(function () {
        Route::get('dashboard', [TimeLogController::class, 'index'])->name('dashboard');
        Route::post('clock-in', [TimeLogController::class, 'store'])->name('clock-in');
        Route::put('clock-out/{timeLog}', [TimeLogController::class, 'update'])->name('clock-out');
        Route::get('logs', [TimeLogController::class, 'history'])->name('logs');
        Route::post('logs/manual', [TimeLogController::class, 'storeManual'])->name('logs.manual');
        Route::get('logs/export', [TimeLogController::class, 'export'])->name('logs.export');
    });

    // Admin Routes
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('companies', [AdminController::class, 'storeCompany'])->name('companies.store');
        Route::get('companies/{company}', [AdminController::class, 'showCompany'])->name('companies.show');
        Route::patch('companies/{company}/archive', [AdminController::class, 'toggleArchive'])->name('companies.archive');
        Route::post('students/assign', [AdminController::class, 'assignStudent'])->name('students.assign');
        Route::delete('students/{user}/remove', [AdminController::class, 'removeStudent'])->name('students.remove');
        Route::get('users', [AdminController::class, 'users'])->name('users');
        Route::delete('users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::post('users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.resetPassword');
        Route::get('logs', [AdminController::class, 'logs'])->name('logs');
    });
});
