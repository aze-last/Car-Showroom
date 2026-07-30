<?php

use App\Http\Middleware\EnsureUserHasGoogleAccount;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsOwner;
use App\Http\Middleware\EnsureUserIsStaff;
use App\Http\Middleware\EnsureUserNotBlocked;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            EnsureUserNotBlocked::class,
        ]);
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'owner' => EnsureUserIsOwner::class,
            'google' => EnsureUserHasGoogleAccount::class,
            'staff' => EnsureUserIsStaff::class,
            'not_blocked' => EnsureUserNotBlocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
