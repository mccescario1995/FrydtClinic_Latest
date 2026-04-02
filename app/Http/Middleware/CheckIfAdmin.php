<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfAdmin
{
    /**
     * Check if the logged in user is a true administrator (not employee/doctor).
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    private function checkIfUserIsAdmin($user)
    {
        // Only allow true admins to access admin panel
        return $user && $user->hasRole('Admin', 'web');
    }

    /**
     * Answer to unauthorized access request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            return redirect()->guest(backpack_url('login'));
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        $user = backpack_user();

        // Redirect employees to employee dashboard
        if ($user->hasRole('Employee', 'web') || $user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        }

        // Redirect doctors to employee dashboard (they use the same portal)
        if ($user->hasRole('Doctor', 'web')) {
            return redirect()->route('employee.dashboard');
        }

        // Only allow true admins to access admin panel
        if (!$user->hasRole('Admin', 'web')) {
            return $this->respondToUnauthorizedRequest($request);
        }

        // Redirect admins to custom admin portal instead of Backpack admin panel
        if ($user->isAdmin()) {
            return redirect()->route('admin-portal.dashboard');
        }

        return $next($request);
    }
}
