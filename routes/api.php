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
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

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

// Admin CRUD API - single ADMIN role (see CLAUDE.md's Auth model).
Route::middleware(['jwt.auth', 'role:ADMIN'])->prefix('admin')->group(function () {
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/about', [AboutItemController::class, 'index']);
    Route::post('/about', [AboutItemController::class, 'store']);
    Route::post('/about/reorder', [AboutItemController::class, 'reorder']);
    Route::put('/about/{aboutItem}', [AboutItemController::class, 'update']);
    Route::delete('/about/{aboutItem}', [AboutItemController::class, 'destroy']);

    Route::get('/tech-icons', [TechIconController::class, 'index']);
    Route::post('/tech-icons', [TechIconController::class, 'store']);
    Route::post('/tech-icons/reorder', [TechIconController::class, 'reorder']);
    Route::put('/tech-icons/{techIcon}', [TechIconController::class, 'update']);
    Route::delete('/tech-icons/{techIcon}', [TechIconController::class, 'destroy']);

    Route::get('/experiences', [ExperienceController::class, 'index']);
    Route::post('/experiences', [ExperienceController::class, 'store']);
    Route::post('/experiences/reorder', [ExperienceController::class, 'reorder']);
    Route::put('/experiences/{experience}', [ExperienceController::class, 'update']);
    Route::delete('/experiences/{experience}', [ExperienceController::class, 'destroy']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::post('/projects/reorder', [ProjectController::class, 'reorder']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    Route::post('/projects/{project}/feature-graphics', [ProjectController::class, 'storeFeatureGraphic']);
    Route::delete('/projects/{project}/feature-graphics/{featureGraphic}', [ProjectController::class, 'destroyFeatureGraphic']);
    Route::post('/projects/{project}/demo-images', [ProjectController::class, 'storeDemoImage']);
    Route::delete('/projects/{project}/demo-images/{demoImage}', [ProjectController::class, 'destroyDemoImage']);

    Route::put('/settings', [SettingController::class, 'update']);
});
