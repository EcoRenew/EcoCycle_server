<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(_DIR_))
    ->withRouting(
        web: _DIR_.'/../routes/web.php',
        api: _DIR_.'/../routes/api.php',
        commands: _DIR_.'/../routes/console.php',
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
