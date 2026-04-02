<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActivityLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log activities for authenticated users
        if (!Auth::check()) {
            return $response;
        }

        $this->logActivity($request, $response);

        return $response;
    }

    /**
     * Log user activity based on the request
     */
    private function logActivity(Request $request, Response $response): void
    {
        $user = Auth::user();
        $method = $request->method();
        $route = $request->route();

        // Skip logging for certain routes
        if ($this->shouldSkipLogging($request)) {
            return;
        }

        $action = $this->determineAction($method, $route);
        $description = $this->generateDescription($request, $action);

        if ($action && $description) {
            try {
                ActivityLogger::log(
                    $action,
                    $description,
                    null,
                    null,
                    null,
                    [
                        'method' => $method,
                        'route' => $route ? $route->getName() : $request->path(),
                        'status_code' => $response->getStatusCode(),
                        'user_agent' => $request->userAgent(),
                    ]
                );
            } catch (\Exception $e) {
                // Log the error but don't break the response
                \Log::error('Failed to log activity: ' . $e->getMessage());
            }
        }
    }

    /**
     * Determine if logging should be skipped for this request
     */
    private function shouldSkipLogging(Request $request): bool
    {
        $skipRoutes = [
            'admin/dashboard',
            'admin/login',
            'admin/logout',
            'api/',
            'debugbar',
            '_debugbar',
        ];

        $path = $request->path();

        foreach ($skipRoutes as $skipRoute) {
            if (str_contains($path, $skipRoute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the action based on HTTP method and route
     */
    private function determineAction(string $method, $route): ?string
    {
        // Check route name for specific actions
        if ($route && $route->getName()) {
            $routeName = $route->getName();

            if (str_contains($routeName, '.store')) {
                return 'create';
            }

            if (str_contains($routeName, '.update')) {
                return 'update';
            }

            if (str_contains($routeName, '.destroy')) {
                return 'delete';
            }

            if (str_contains($routeName, '.show') || str_contains($routeName, '.edit')) {
                return 'view';
            }
        }

        // Fallback to HTTP method
        switch ($method) {
            case 'POST':
                return 'create';
            case 'PUT':
            case 'PATCH':
                return 'update';
            case 'DELETE':
                return 'delete';
            case 'GET':
                return 'view';
            default:
                return null;
        }
    }

    /**
     * Generate a human-readable description for the activity
     */
    private function generateDescription(Request $request, ?string $action): ?string
    {
        $route = $request->route();

        if (!$route || !$route->getName()) {
            return null;
        }

        $routeName = $route->getName();
        $segments = explode('.', $routeName);

        if (count($segments) < 2) {
            return null;
        }

        $resource = $segments[0];
        $actionVerb = $segments[1];

        // Convert resource names to readable format
        $resourceNames = [
            'patient' => 'Patient',
            'appointment' => 'Appointment',
            'billing' => 'Bill',
            'laboratory-result' => 'Laboratory Result',
            'service' => 'Service',
            'inventory' => 'Inventory Item',
            'user' => 'User',
            'role' => 'Role',
            'permission' => 'Permission',
        ];

        $resourceName = $resourceNames[$resource] ?? ucfirst(str_replace('-', ' ', $resource));

        // Convert action to description
        $actionDescriptions = [
            'index' => "Viewed {$resourceName} list",
            'create' => "Accessed {$resourceName} creation form",
            'store' => "Created new {$resourceName}",
            'show' => "Viewed {$resourceName} details",
            'edit' => "Accessed {$resourceName} edit form",
            'update' => "Updated {$resourceName}",
            'destroy' => "Deleted {$resourceName}",
        ];

        return $actionDescriptions[$actionVerb] ?? "Performed {$action} on {$resourceName}";
    }
}
