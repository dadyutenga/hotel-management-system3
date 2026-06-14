<?php

use App\Http\Middleware\BuildingScopeMiddleware;
use App\Http\Middleware\CheckStaffSession;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware — applied to every request
        $middleware->append(SecurityHeaders::class);

        // Localization middleware — sets app locale from session
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'staff.session' => CheckStaffSession::class,
            'building.scope' => BuildingScopeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
