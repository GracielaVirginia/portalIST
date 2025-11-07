<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// use App\Http\Middleware\EnsureAuthenticated; // si lo necesitas

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(EnsureAuthenticated::class); // opcional

$middleware->alias([
    'admin.auth' => \App\Http\Middleware\EnsureAdminAuthenticated::class,
     'update.activity' => \App\Http\Middleware\UpdateLoginActivity::class,
       'must.be.validated' => \App\Http\Middleware\EnsureUserIsValidated::class,
]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
