<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the admin dashboard
|
*/

// Admin Authentication
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);

// User Management Routes
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    // Authenticated admin info endpoint
    Route::get('me', [App\Http\Controllers\Admin\AuthController::class, 'me']);
    // User routes
    Route::apiResource('users', UserController::class);

    // Product routes
    Route::apiResource('products', ProductController::class);

    // Material routes
    Route::apiResource('materials', MaterialController::class);

    // Category routes
    Route::apiResource('categories', CategoryController::class);

    // Dashboard routes
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/activities', [DashboardController::class, 'activities']);

    // Request routes (to be implemented)
    // Route::apiResource('requests', RequestController::class);
});
