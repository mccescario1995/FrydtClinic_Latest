<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use CrudTrait;
    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected by the activity
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering by user type
     */
    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by model type
     */
    public function scopeByModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Get formatted action description
     */
    public function getFormattedActionAttribute()
    {
        $actions = [
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'view' => 'Viewed',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'export' => 'Exported',
            'import' => 'Imported',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Get user type badge color
     */
    public function getUserTypeColorAttribute()
    {
        $colors = [
            'staff' => 'primary',
            'patient' => 'success',
            'system' => 'secondary',
        ];

        return $colors[$this->user_type] ?? 'secondary';
    }

    /**
     * Get action badge color
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'view' => 'info',
            'login' => 'primary',
            'logout' => 'secondary',
            'export' => 'info',
            'import' => 'info',
        ];

        return $colors[$this->action] ?? 'secondary';
    }
}
