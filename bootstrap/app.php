<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->alias([
        //     'check.role' => \App\Http\Middleware\CheckRole::class,
        // ]);
        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
            'cors' => \Illuminate\Http\Middleware\HandleCors::class, // Correctly added here
        ]);

        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
