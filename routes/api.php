<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AIController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
// Include admin routes
require __DIR__ . '/api_admin.php';
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Events\Verified;


Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect(env('FRONTEND_URL') . '/register'); 
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }
    return redirect(env('FRONTEND_URL') . '/login');

})->middleware('signed')->name('verification.verify');

Route::post('users/register', [AuthController::class, 'register']);
Route::post('users/login', [AuthController::class, 'login']);
Route::post('users/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

// User profile endpoint
Route::get('users/me', [AuthController::class, 'me'])
    ->middleware('auth:sanctum');


Route::post('/products/{product}/produce', [ProductController::class, 'produce']);
Route::apiResource('products', ProductController::class);





Route::middleware(['auth:sanctum',EnsureEmailIsVerified::class])->group(function () {
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
    // Phone numbers for the authenticated user
    Route::get('/phone-numbers', [\App\Http\Controllers\PhoneNumberController::class, 'index']);
    Route::post('/phone-numbers', [\App\Http\Controllers\PhoneNumberController::class, 'store']);
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
Route::post('/ai/diy-helper', [AIController::class, 'generateDIY']);
