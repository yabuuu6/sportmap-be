<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SportsFieldController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookmarkController;
use App\Http\Middleware\JwtMiddleware;

// 🔓 Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', fn () => ['message' => 'API OK']);

// 🔓 Public Sports Field Routes
Route::get('/fields/recommendation', [SportsFieldController::class, 'recommendation']);
Route::get('/fields/{sportsField}', [SportsFieldController::class, 'show']);
Route::get('/fields/{sportsField}/reviews', [ReviewController::class, 'index']);

// 🔐 Protected Routes (JWT required)
Route::middleware([JwtMiddleware::class])->group(function () {
    // 👤 Authenticated User
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🏟 Sports Field Management
    Route::get('/fields', [SportsFieldController::class, 'index']);
    Route::post('/fields', [SportsFieldController::class, 'store']);
    Route::get('/fields/{sportsField}', [SportsFieldController::class, 'show']);
    Route::put('/fields/{sportsField}', [SportsFieldController::class, 'update']);
    Route::delete('/fields/{sportsField}', [SportsFieldController::class, 'destroy']);
    Route::post('/fields/{id}/photos', [SportsFieldController::class, 'uploadPhoto']);
    Route::put('/fields/{id}/photos', [SportsFieldController::class, 'uploadPhoto']);
    Route::put('/fields/{id}/verify', [SportsFieldController::class, 'verify']);
    Route::put('/fields/{id}', [SportsFieldController::class, 'update']);
    
    // ⭐ Review (Rate & Comment)
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']); 
    Route::post('/fields/{sportsField}/reviews', [ReviewController::class, 'store']);
    Route::get('/fields/{sportsField}/reviews', [ReviewController::class, 'index']);

    // 📌 Bookmark
    Route::post('/bookmarks/{fieldId}/toggle', [BookmarkController::class, 'toggle']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
});

