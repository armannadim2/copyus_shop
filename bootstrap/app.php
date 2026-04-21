<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\B2BApproved;
use App\Http\Middleware\SetLocale;
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

        // Global middleware — runs on every request
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Named aliases for route middleware
        $middleware->alias([
            'approved' => B2BApproved::class,
            'admin'    => AdminMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
