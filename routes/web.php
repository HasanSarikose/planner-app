<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AccountController;

Route::get('/', function () {
    return auth()->check() ? redirect('/planner') : redirect('/login');
});

Route::get('/login', function () {
    return auth()->check() ? redirect('/planner') : view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/planner', [TaskController::class, 'index']);

    // Tasks
    Route::get('/tasks', [TaskController::class, 'getTasks']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);

    // Notes
    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::patch('/notes/{id}/toggle', [NoteController::class, 'toggle']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);
    Route::patch('/notes/{id}', [NoteController::class, 'update']);

    // Account
    Route::get('/account/info', [AccountController::class, 'info']);
    Route::post('/account/password', [AccountController::class, 'changePassword']);
});
