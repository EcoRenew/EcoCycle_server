<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Define middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Exclude specific URIs from CSRF protection
        $middleware->validateCsrfTokens(except: [
            '/webhook/stripe',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Customize exception handling here if needed
    })
    ->create();
