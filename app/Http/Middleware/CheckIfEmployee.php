<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!backpack_auth()->check()) {
            return redirect()->guest(backpack_url('login'));
        }

        $user = backpack_user();

        // Check if user has Employee role OR is an employee by user_type
        if (!$user->hasRole('Employee', 'web') && !$user->hasRole('Doctor', 'web') && !$user->isEmployee()) {
            abort(403, 'Access denied. Employee access required.');
        }

        return $next($request);
    }
}
