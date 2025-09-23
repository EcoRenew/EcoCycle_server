<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\DashboardController;
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
    Route::apiResource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // FAQs
    Route::get('faqs', [App\Http\Controllers\Admin\FaqController::class, 'index']);
    Route::post('faqs', [App\Http\Controllers\Admin\FaqController::class, 'store']);
    Route::put('faqs/{id}', [App\Http\Controllers\Admin\FaqController::class, 'update']);
    Route::delete('faqs/{id}', [App\Http\Controllers\Admin\FaqController::class, 'destroy']);

    // Events
    Route::get('events', [App\Http\Controllers\Admin\EventController::class, 'index']);
    Route::post('events', [App\Http\Controllers\Admin\EventController::class, 'store']);
    Route::put('events/{id}', [App\Http\Controllers\Admin\EventController::class, 'update']);
    Route::delete('events/{id}', [App\Http\Controllers\Admin\EventController::class, 'destroy']);

    // Event registrations
    Route::get('event-registrations', [App\Http\Controllers\Admin\EventRegistrationController::class, 'index']);
    Route::post('event-registrations/{id}/status', [App\Http\Controllers\Admin\EventRegistrationController::class, 'updateStatus']);

    // Dashboard routes
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/activities', [DashboardController::class, 'activities']);

    // Invoice routes
    Route::get('invoices', [App\Http\Controllers\Admin\InvoiceController::class, 'index']);
    Route::get('invoices/{id}', [App\Http\Controllers\Admin\InvoiceController::class, 'show']);

    // Email logs for requests
    Route::get('requests/{id}/emails', [App\Http\Controllers\Admin\EmailLogController::class, 'index']);
    Route::post('requests/{id}/emails/resend', [App\Http\Controllers\Admin\EmailLogController::class, 'resend']);

    // Requests routes (CRUD + status update)
    Route::get('requests', [App\Http\Controllers\RequestController::class, 'index']);
    Route::post('requests', [App\Http\Controllers\RequestController::class, 'store']);
    Route::get('requests/{id}', [App\Http\Controllers\RequestController::class, 'adminShow']);
    Route::put('requests/{id}', [App\Http\Controllers\RequestController::class, 'adminUpdate']);
    Route::delete('requests/{id}', [App\Http\Controllers\RequestController::class, 'adminDestroy']);
    Route::post('requests/{id}/status', [App\Http\Controllers\RequestController::class, 'updateStatus']);
});
