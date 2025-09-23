<?php
return [
    'api_key' => [
        'secret' => env('STRIPE_SECRET'),
    ],
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),
];
