<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebHook;
use App\Http\Controllers\AIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
// Include admin routes
require __DIR__ . '/api_admin.php';

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('users/register', [AuthController::class, 'register']);
Route::post('users/login', [AuthController::class, 'login']);
Route::post('users/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');


Route::post('/products/{product}/produce', [ProductController::class, 'produce']);
Route::apiResource('products', ProductController::class);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
    Route::post('/products/pay', [StripeController::class, 'pay']);
});
// Materials
Route::get('/materials', [RequestController::class, 'getMaterials']);

// User addresses (authenticated)
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
});

// Requests (User)
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::apiResource('requests', RequestController::class)->only(['store', 'show']);
    Route::get('user/requests', [RequestController::class, 'getUserRequests']);
});

// Requests (Admin)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('admin/requests', [RequestController::class, 'index']);
    Route::post('admin/requests/{id}/status', [RequestController::class, 'updateStatus']);
});

// Requests (Collector)
Route::middleware(['auth:sanctum', 'role:collector'])->group(function () {
    Route::get('collector/assignments', [RequestController::class, 'getCollectorAssignments']);
    Route::post('collector/requests/{id}/status', [RequestController::class, 'updateStatus']);
});
