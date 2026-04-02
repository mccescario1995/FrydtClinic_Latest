<?php
namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class LoginRedirectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for logout events to redirect to homepage
        Event::listen('Illuminate\Auth\Events\Logout', function ($event) {
            // Redirect to homepage after logout
            if (!request()->isMethod('post') || !request()->expectsJson()) {
                redirect()->route('homepage')->send();
            }
        });

        // Override Backpack's redirect after login by binding custom controller
        $this->app->bind('Backpack\CRUD\app\Http\Controllers\Auth\LoginController', function ($app) {
            return new class extends \Backpack\CRUD\app\Http\Controllers\Auth\LoginController {
                protected function authenticated($request, $user)
                {
                    // Check user type first (more reliable than roles for our custom system)
                    if ($user->isAdmin() || $user->hasRole('Admin')) {
                        return redirect()->route('admin-portal.dashboard');
                    }
                    if ($user->isEmployee() || $user->hasRole('Employee')) {
                        return redirect()->route('employee.dashboard');
                    }
                    if ($user->isPatient() || $user->hasRole('Patient')) {
                        return redirect()->route('patient.dashboard');
                    }

                    // Fallback to Backpack admin for any other authenticated users
                    return redirect(config('backpack.base.route_prefix', 'admin') . '/dashboard');
                }
            };
        });

        // Override Backpack's logout redirect by binding custom controller
        $this->app->bind('Backpack\CRUD\app\Http\Controllers\Auth\LogoutController', function ($app) {
            return new class extends \Backpack\CRUD\app\Http\Controllers\Auth\LogoutController {
                protected function loggedOut($request)
                {
                    return redirect()->route('homepage');
                }
            };
        });

        // $this->app->bind('Backpack\CRUD\app\Http\Controllers\Auth\LoginController', function ($app) {
        //     $controller = new \App\Http\Controllers\Auth\LoginController();

        //     $controller->redirectTo = function () {
        //         if (auth()->user()->hasRole('Patient')) {
        //             return route('patient.dashboard');
        //         }
        //         return config('backpack.base.route_prefix', 'admin') . '/dashboard';
        //     };

        //     return $controller;
        // });
    }
}
