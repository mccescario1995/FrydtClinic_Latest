<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectPatientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip middleware for certain routes to prevent redirect loops
        if ($request->is('/') || $request->is('patient') || $request->is('patient/*') ||
            $request->is('admin/logout') || $request->is('logout')) {
            return $next($request);
        }

        // If user is authenticated and has Patient role
        if (backpack_auth()->check() && backpack_auth()->user()->hasRole('Patient')) {
            // If they're trying to access admin routes, redirect to patient dashboard
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('patient.dashboard');
            }
        }

        return $next($request);
    }
}
