<?php
return [
    'api_key' => [
        'secret' => env('STRIPE_SECRET')
    ],
    'success_url' => env('STRIPE_SUCCESS_URL', config('app.url') . '/success'),
    'cancel_url' => env('STRIPE_CANCEL_URL', config('app.url') . '/cancel'),
];