<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ReviewController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/update-url', [AuthController::class, 'updateUrl']);
    Route::post('/parse-reviews', [ReviewController::class, 'parseYandexReviews']);
});

Route::prefix('v1')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'api работает!']);
    });
});