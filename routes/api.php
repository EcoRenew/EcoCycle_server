<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebHook;
use App\Http\Controllers\AIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('users/register', [AuthController::class, 'register']);
Route::post('users/login', [AuthController::class, 'login']);
Route::post('users/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');


Route::post('/products/{product}/produce', [ProductController::class, 'produce']);
Route::apiResource('products', ProductController::class);

Route::post('stripe/webhook', [StripeWebHook::class, 'handleWebHook']);
Route::post('/products/pay', [StripeController::class, 'pay']);

Route::post('/ai/diy-helper', [AIController::class, 'generateDIY']);
// Route::match(['get', 'post'], '/ai/diy-helper', [AIController::class, 'generateDIY']);
