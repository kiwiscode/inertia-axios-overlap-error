<?php

use App\Http\Controllers\HeartBeatController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Heartbeat
Route::get('/heartbeat', [HeartBeatController::class, 'heartbeat'])->name('heartbeat');

// Tasks
Route::prefix('/')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
});