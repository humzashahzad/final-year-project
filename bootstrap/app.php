<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            $centralDomain = config('tenancy.central_domains');

            foreach ($centralDomain as $domain) {
                app('router')->middleware('web')->domain($domain)->group(base_path('routes/web.php'));
            }

            app('router')->middleware('web')->group(base_path('routes/tenant.php'));
        },
        // web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('universal', []);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
    
    
    // ->withRouting(
    //     web: __DIR__.'/../routes/web.php',
    //     commands: __DIR__.'/../routes/console.php',
    //     health: '/up',
    // )
    // ->withMiddleware(function (Middleware $middleware): void {
    //     //
    // })
    // ->withExceptions(function (Exceptions $exceptions): void {
    //     //
    // })->create();
