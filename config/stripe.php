<?php
return [
    'api_key' => [
        'secret' => env('STRIPE_SECRET')
    ],
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
];