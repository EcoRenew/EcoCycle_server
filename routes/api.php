<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\PublicContentController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\PhoneNumberController;
use App\Http\Controllers\DonationController;

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

// Donation routes
Route::apiResource('donations', DonationController::class);





Route::middleware(['auth:sanctum', EnsureEmailIsVerified::class])->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
    Route::post('/products/pay', [StripeController::class, 'pay']);
    Route::post('/cart/buy-with-points', [CartController::class, 'buyWithPoints']);
});
// Materials
Route::get('/materials', [RequestController::class, 'getMaterials']);
// User addresses (authenticated)
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    // Phone numbers for the authenticated user
    Route::get('/phone-numbers', [PhoneNumberController::class, 'index']);
    Route::post('/phone-numbers', [PhoneNumberController::class, 'store']);
});

// Public FAQs and Events
Route::get('faqs', [PublicContentController::class, 'faqs']);
Route::get('events', [PublicContentController::class, 'events']);
Route::post('events/register', [PublicContentController::class, 'register']);

// Phones (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/phones', [PhoneController::class, 'index']);
    Route::post('/phones', [PhoneController::class, 'store']);
});

// Requests (User)
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::apiResource('requests', RequestController::class)->only(['store', 'show']);
    // Route::get('user/requests', [RequestController::class, 'getUserRequests']);
    Route::get('user/dashboard', [RequestController::class, 'getUserDashboard']);
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

//AI Route
Route::post('/ai/diy-helper', [AIController::class, 'generateDIY']);

//Requests(google)
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Categories
Route::get('/categories', [RequestController::class, 'getCategories']);

// CORS preflight: allow OPTIONS for any api route
Route::options('/{any}', function () {
    return response()->noContent();
})->where('any', '.*');

//Donation Route
Route::post('/donations', [DonationController::class, 'store'])->middleware(['auth:sanctum', 'role:user']);
// Route::post('/donations', [DonationController::class, 'store']);

//Community
Route::get('/community/posts', [CommunityController::class, 'index']);
Route::post('/community/posts', [CommunityController::class, 'store']);


