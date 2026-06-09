<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'api/*',
        ]);

        $middleware->alias([
            'premium' => \App\Http\Middleware\PremiumAccess::class,
        ]);

        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Handle CORS explicitly for API
        // In Laravel 11/12 this is usually auto-handled if allowed patterns are set in .env or via trust proxies
        // But we can force it here just in case.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON for unauthenticated API requests instead of redirecting to a web login route
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $e->getMessage() ?: 'Unauthenticated.'], 401);
            }
        });

    })->create();
