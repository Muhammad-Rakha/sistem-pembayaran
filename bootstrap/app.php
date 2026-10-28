<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Spatie laravel-permission v8 no longer registers these aliases itself,
        // so routes using `role:` / `permission:` must declare them here.
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Webhook endpoints are exempt from CSRF; they use provider signature auth instead (§10.9)
        $middleware->validateCsrfTokens(except: [
            'webhooks/midtrans',
            'webhooks/simulator',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
