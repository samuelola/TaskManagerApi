<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         $middleware->alias([
            'jwt.refresh' => \App\Http\Middleware\AutoRefreshToken::class,
            'jwt.version' => \App\Http\Middleware\CheckTokenVersion::class,
            'check.jwt' =>   \App\Http\Middleware\CheckJWT::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Throttle errors
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            return response()->json([
                'message' => 'Too Many Attempts'
            ], 429);
        });
    })->create();
