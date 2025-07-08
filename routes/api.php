<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SportsFieldController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\JwtMiddleware;

// 🔓 Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Lapangan Olahraga
Route::get('/fields', [SportsFieldController::class, 'index']); // with filter
Route::get('/fields/{sportsField}', [SportsFieldController::class, 'show']);
Route::get('/fields/{sportsField}/reviews', [ReviewController::class, 'index']);

// Jika ingin hanya user login yang boleh menambah, pindah ke grup bawah
Route::post('/fields', [SportsFieldController::class, 'store']);

// 🔐 Protected Routes (JWT required)
Route::middleware([JwtMiddleware::class])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Sports Field
    Route::put('/fields/{sportsField}', [SportsFieldController::class, 'update']);
    Route::delete('/fields/{sportsField}', [SportsFieldController::class, 'destroy']);
    Route::post('/fields/{id}/photos', [SportsFieldController::class, 'uploadPhoto']);

    // Review
    Route::post('/fields/{id}/reviews', [ReviewController::class, 'store']);
});

// ✅ Endpoint untuk testing
Route::get('/test', fn () => ['message' => 'API OK']);
