<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class ActivityLogger
{
    /**
     * Log a user activity
     */
    public static function log(
        string $action,
        string $description,
        $model = null,
        array $oldValues = null,
        array $newValues = null,
        array $metadata = null
    ): ActivityLog {
        $user = auth()->user();
        $userType = 'system';

        if ($user) {
            $userType = $user->hasRole('Patient') ? 'patient' : 'staff';
        }

        $activityData = [
            'user_id' => $user ? $user->id : null,
            'user_type' => $userType,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'session_id' => Session::getId(),
            'metadata' => $metadata,
        ];

        if ($model) {
            $activityData['model_type'] = get_class($model);
            $activityData['model_id'] = $model->getKey();
        }

        if ($oldValues) {
            $activityData['old_values'] = $oldValues;
        }

        if ($newValues) {
            $activityData['new_values'] = $newValues;
        }

        return ActivityLog::create($activityData);
    }

    /**
     * Log model creation
     */
    public static function logCreated($model, string $description = null): ActivityLog
    {
        $modelName = class_basename($model);
        $description = $description ?? "Created new {$modelName}";

        return self::log('create', $description, $model, null, $model->toArray());
    }

    /**
     * Log model update
     */
    public static function logUpdated($model, array $oldValues = null, string $description = null): ActivityLog
    {
        $modelName = class_basename($model);
        $description = $description ?? "Updated {$modelName}";

        return self::log('update', $description, $model, $oldValues, $model->toArray());
    }

    /**
     * Log model deletion
     */
    public static function logDeleted($model, string $description = null): ActivityLog
    {
        $modelName = class_basename($model);
        $description = $description ?? "Deleted {$modelName}";

        return self::log('delete', $description, $model, $model->toArray());
    }

    /**
     * Log model view
     */
    public static function logViewed($model, string $description = null): ActivityLog
    {
        $modelName = class_basename($model);
        $description = $description ?? "Viewed {$modelName}";

        return self::log('view', $description, $model);
    }

    /**
     * Log user login
     */
    public static function logLogin($user = null): ActivityLog
    {
        $user = $user ?? auth()->user();
        $description = "User logged in";

        return self::log('login', $description, $user);
    }

    /**
     * Log user logout
     */
    public static function logLogout($user = null): ActivityLog
    {
        $user = $user ?? auth()->user();
        $description = "User logged out";

        return self::log('logout', $description, $user);
    }

    /**
     * Log export action
     */
    public static function logExport(string $exportType, string $description = null): ActivityLog
    {
        $description = $description ?? "Exported {$exportType} data";

        return self::log('export', $description, null, null, null, ['export_type' => $exportType]);
    }

    /**
     * Log import action
     */
    public static function logImport(string $importType, int $recordsCount, string $description = null): ActivityLog
    {
        $description = $description ?? "Imported {$recordsCount} {$importType} records";

        return self::log('import', $description, null, null, null, [
            'import_type' => $importType,
            'records_count' => $recordsCount
        ]);
    }

    /**
     * Log system event
     */
    public static function logSystemEvent(string $event, string $description, array $metadata = null): ActivityLog
    {
        return self::log('system', $description, null, null, null, array_merge($metadata ?? [], [
            'event_type' => $event
        ]));
    }

    /**
     * Get recent activities for a user
     */
    public static function getUserActivities($userId, int $limit = 50)
    {
        return ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by date range
     */
    public static function getActivitiesByDateRange($startDate, $endDate, array $filters = [])
    {
        $query = ActivityLog::whereBetween('created_at', [$startDate, $endDate]);

        if (isset($filters['user_type'])) {
            $query->byUserType($filters['user_type']);
        }

        if (isset($filters['action'])) {
            $query->byAction($filters['action']);
        }

        if (isset($filters['model_type'])) {
            $query->byModelType($filters['model_type']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get activity statistics
     */
    public static function getActivityStats($days = 30)
    {
        $startDate = now()->subDays($days);

        return [
            'total_activities' => ActivityLog::where('created_at', '>=', $startDate)->count(),
            'user_logins' => ActivityLog::where('action', 'login')->where('created_at', '>=', $startDate)->count(),
            'records_created' => ActivityLog::where('action', 'create')->where('created_at', '>=', $startDate)->count(),
            'records_updated' => ActivityLog::where('action', 'update')->where('created_at', '>=', $startDate)->count(),
            'records_deleted' => ActivityLog::where('action', 'delete')->where('created_at', '>=', $startDate)->count(),
            'most_active_users' => ActivityLog::selectRaw('user_id, COUNT(*) as activity_count')
                ->where('created_at', '>=', $startDate)
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderBy('activity_count', 'desc')
                ->limit(5)
                ->with('user')
                ->get(),
        ];
    }
}
