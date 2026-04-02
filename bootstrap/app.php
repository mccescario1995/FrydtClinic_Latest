<?php

use App\Http\Middleware\RedirectPatientMiddleware;
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
        // Global middleware

        // $middleware->redirectGuestsTo(fn() => route('backpack.auth.login'));

        // Apply middleware to web routes
        $middleware->web(append: [
            \App\Http\Middleware\RedirectPatientMiddleware::class,
            \App\Http\Middleware\ActivityLoggingMiddleware::class,
            \App\Http\Middleware\CheckPinVerification::class,
        ]);

        // Middleware aliases
        $middleware->alias([
            'auth'               => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic'         => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session'       => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers'      => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'                => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'              => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'password.confirm'   => \Illuminate\Auth\Middleware\RequirePassword::class,
            'precognitive'       => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            'signed'             => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'           => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'           => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            // Backpack middleware
            'admin'              => \Backpack\CRUD\App\Http\Middleware\CheckIfAdmin::class,

            // Spatie Permission middleware
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            // Custom middleware
            'redirect.patient'   => RedirectPatientMiddleware::class,
            'employee'           => \App\Http\Middleware\CheckIfEmployee::class,
            'admin-portal'       => \App\Http\Middleware\AdminMiddleware::class,

            //Payment middleware
            'payment.owner' => \App\Http\Middleware\PaymentOwnershipMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
