<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPinVerification
{
    /**
     * Routes that should skip OTP verification
     */
    private const EXCLUDED_ROUTES = [
        'verify-otp',
        'set-phone',
        'resend-otp',
        'logout',
        'login',
        'password/*',
    ];

    /**
     * Asset paths that should be skipped
     */
    private const ASSET_PATHS = [
        '/css/',
        '/js/',
        '/images/',
        '/fonts/',
        '/assets/',
        '/storage/',
        '/favicon',
    ];

    /**
     * Asset file extensions that should be skipped
     */
    private const ASSET_EXTENSIONS = [
        '.css',
        '.js',
        '.png',
        '.jpg',
        '.jpeg',
        '.gif',
        '.svg',
        '.ico',
        '.woff',
        '.woff2',
        '.ttf',
        '.eot',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for asset requests
        if ($this->shouldSkipRequest($request)) {
            return $next($request);
        }

        // Skip middleware for unauthenticated users
        if (!backpack_auth()->check()) {
            return $next($request);
        }

        $user = backpack_auth()->user()->load(['patientProfile', 'employeeProfile']);

        // Skip verification for admin users
        if ($user->user_type === 'admin') {
            return $next($request);
        }

        // Check user's verification status and redirect accordingly
        $redirectRoute = $this->getRequiredRedirect($user, $request);

        if ($redirectRoute) {
            return redirect()->route($redirectRoute);
        }

        return $next($request);
    }

    /**
     * Check if the request should be skipped by this middleware
     */
    private function shouldSkipRequest(Request $request): bool
    {
        // Skip asset requests
        if ($this->isAssetRequest($request)) {
            return true;
        }

        // Skip excluded routes
        foreach (self::EXCLUDED_ROUTES as $route) {
            if ($request->is($route) || $request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the request is for an asset
     */
    private function isAssetRequest(Request $request): bool
    {
        $path = $request->getPathInfo();

        // Check for asset paths
        foreach (self::ASSET_PATHS as $assetPath) {
            if (str_starts_with($path, $assetPath)) {
                return true;
            }
        }

        // Check for asset file extensions
        foreach (self::ASSET_EXTENSIONS as $extension) {
            if (str_ends_with($path, $extension)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the required redirect route for the user, or null if no redirect needed
     */
    private function getRequiredRedirect($user, Request $request): ?string
    {
        $phone = $this->getUserPhone($user);


        // Check if OTP verification is required
        if ($this->requiresOtpVerification($user)) {
            return $request->is('verify-otp') ? null : 'verify-otp';
        }

        // Check if phone is missing or invalid
        if ($this->isPhoneMissing($phone)) {
            return $request->is('set-phone') ? null : 'set-phone';
        }

        

        return null;
    }

    /**
     * Check if phone is missing or invalid
     */
    private function isPhoneMissing(?string $phone): bool
    {
        return empty($phone) || $phone === 'Not provided'; 
    }

    /**
     * Check if user requires OTP verification
     */
    private function requiresOtpVerification($user): bool
    {
        return !$user->otp_verified_at || !$user->otp_verified_at->isToday();
    }

    /**
     * Get user's phone number
     */
    private function getUserPhone($user): ?string
    {
        if ($user->user_type === 'patient' && $user->patientProfile) {
            return $user->patientProfile->phone;
        }

        if ($user->user_type === 'employee' && $user->employeeProfile) {
            return $user->employeeProfile->phone;
        }

        return null;
    }
}
