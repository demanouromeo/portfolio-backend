<?php

use App\Http\Controllers\AboutItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TechIconController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/logout', [AuthController::class, 'logout']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
});

// Public read API - powers the portfolio's public-facing site, no auth required.
Route::get('/profile', [ProfileController::class, 'show']);
Route::get('/about', [AboutItemController::class, 'index']);
Route::get('/tech-icons', [TechIconController::class, 'index']);
Route::get('/experiences', [ExperienceController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/settings', [SettingController::class, 'index']);
