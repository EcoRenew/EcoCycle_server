<?php

use App\Http\Controllers\StripeWebHook;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [StripeWebHook::class, 'handleWebHook']);
