<?php

use App\Http\Controllers\TimerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/timer/start', [TimerController::class, 'start']);
    Route::post('/timer/stop', [TimerController::class, 'stop']);
    Route::get('/timer/status', [TimerController::class, 'status']);
});

