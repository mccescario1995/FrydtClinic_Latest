<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPinVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip OTP verification for certain routes
        $excludedRoutes = [
            'verify-otp',
            'set-phone',
            'resend-otp',
            'logout',
            'login',
            'password/*',
        ];

        foreach ($excludedRoutes as $route) {
            if ($request->is($route) || $request->routeIs($route)) {
                return $next($request);
            }
        }

        // Check if user is authenticated using backpack guard
        if (!backpack_auth()->check()) {
            return $next($request);
        }

        $user = backpack_auth()->user();

        // Check phone and OTP status
        $phone = $this->getUserPhone($user);

        // Skip verification for admin users
        if ($user->user_type === 'admin') {
            return $next($request);
        }

        if (!$phone || $phone === 'Not provided') {
            // User doesn't have a phone set, redirect to set phone page
            if (!$request->is('set-phone')) {
                return redirect()->route('set-phone');
            }
        } elseif (!$user->otp_verified_at || !$user->otp_verified_at->isToday()) {
            // User has phone but OTP not verified today, redirect to verify OTP page
            if (!$request->is('verify-otp')) {
                return redirect()->route('verify-otp');
            }
        }

        return $next($request);
    }

    /**
     * Get user's phone number
     */
    private function getUserPhone($user)
    {
        if ($user->user_type === 'patient' && $user->patientProfile) {
            return $user->patientProfile->phone;
        } elseif ($user->user_type === 'employee' && $user->employeeProfile) {
            return $user->employeeProfile->phone;
        } elseif ($user->user_type === 'admin') {
            // Admins don't need phone verification - they can access directly
            return 'admin_verified';
        }
        return null;
    }
}
