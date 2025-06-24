<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar alias para los middleware personalizados
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'employee' => \App\Http\Middleware\IsEmployee::class,
        ]);

        // Middleware para las rutas web
        $middleware->web(append: [
            // Aquí puedes agregar middleware adicionales si es necesario
        ]);

        // Middleware para las rutas API
        $middleware->api(prepend: [
            // Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
