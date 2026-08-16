<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
});
