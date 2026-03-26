<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeLogController;
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
    
    Route::get('dashboard', [TimeLogController::class, 'index'])->name('dashboard');
    Route::post('clock-in', [TimeLogController::class, 'store'])->name('clock-in');
    Route::put('clock-out/{timeLog}', [TimeLogController::class, 'update'])->name('clock-out');
    Route::get('logs', [TimeLogController::class, 'history'])->name('logs');
    Route::get('logs/export', [TimeLogController::class, 'export'])->name('logs.export');
});
