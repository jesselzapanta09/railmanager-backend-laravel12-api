<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Root ──────────────────────────────────────────────────────────
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'RailManager API v2.0',
    ]);
});

// ── About ─────────────────────────────────────────────────────────
Route::get('/about', [AboutController::class, 'index']);

// ── Auth Routes ───────────────────────────────────────────────────
Route::post('/register',            [AuthController::class, 'register']);
Route::post('/login',               [AuthController::class, 'login']);
Route::post('/logout',              [AuthController::class, 'logout'])->middleware('auth.jwt');
Route::get('/verify-email',         [AuthController::class, 'verifyEmail']);
Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
Route::post('/forgot-password',     [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',      [AuthController::class, 'resetPassword']);
Route::post('/change-password',     [AuthController::class, 'changePassword'])->middleware('auth.jwt');
Route::post('/update-profile',      [AuthController::class, 'updateProfile'])->middleware('auth.jwt');

// ── Train Routes ──────────────────────────────────────────────────
Route::get('/trains',         [TrainController::class, 'index'])->middleware('auth.jwt');
Route::get('/trains/{id}',    [TrainController::class, 'show'])->middleware('auth.jwt');
Route::post('/trains',        [TrainController::class, 'store'])->middleware(['auth.jwt', 'admin']);
Route::put('/trains/{id}',    [TrainController::class, 'update'])->middleware(['auth.jwt', 'admin']);
Route::delete('/trains/{id}', [TrainController::class, 'destroy'])->middleware(['auth.jwt', 'admin']);

// ── User Routes ───────────────────────────────────────────────────
Route::get('/users',         [UserController::class, 'index'])->middleware(['auth.jwt', 'admin']);
Route::get('/users/{id}',    [UserController::class, 'show'])->middleware(['auth.jwt', 'admin']);
Route::post('/users',        [UserController::class, 'store'])->middleware(['auth.jwt', 'admin']);
Route::put('/users/{id}',    [UserController::class, 'update'])->middleware(['auth.jwt', 'admin']);
Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware(['auth.jwt', 'admin']);

// ── 404 fallback ──────────────────────────────────────────────────
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found',
    ], 404);
});