<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratoryResult extends Model
{

    protected $fillable = [
        'patient_id',
        'ordered_by',
        'performed_by',
        'reviewed_by',
        'test_name',
        'test_category',
        'test_code',
        'test_description',
        'sample_type',
        'sample_type_other',
        'sample_collection_date_time',
        'sample_id',
        'test_result',
        'result_value',
        'result_unit',
        'reference_range',
        'result_status',
        'test_ordered_date_time',
        'test_performed_date_time',
        'result_available_date_time',
        'result_reviewed_date_time',
        'clinical_indication',
        'interpretation',
        'comments',
        'qc_passed',
        'qc_notes',
        'test_cost',
        'covered_by_philhealth',
        'philhealth_coverage_amount',
        'requires_follow_up',
        'follow_up_instructions',
        'follow_up_date',
        'test_status',
        'urgent',
        'stat',
        'rejection_reason',
        'rejected_date_time',
    ];

    protected $casts = [
        'sample_collection_date_time' => 'datetime',
        'test_ordered_date_time' => 'datetime',
        'test_performed_date_time' => 'datetime',
        'result_available_date_time' => 'datetime',
        'result_reviewed_date_time' => 'datetime',
        'rejected_date_time' => 'datetime',
        'follow_up_date' => 'date',
        'urgent' => 'boolean',
        'stat' => 'boolean',
        'qc_passed' => 'boolean',
        'covered_by_philhealth' => 'boolean',
        'requires_follow_up' => 'boolean',
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function orderingProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function performingTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function reviewingProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Accessors & Mutators
    public function getFullSampleTypeAttribute(): string
    {
        return $this->sample_type === 'other' ? $this->sample_type_other : $this->sample_type;
    }

    public function getResultDisplayAttribute(): string
    {
        $result = $this->result_value ?: '';
        if ($this->result_unit) {
            $result .= ' ' . $this->result_unit;
        }
        if ($this->reference_range) {
            $result .= ' (Ref: ' . $this->reference_range . ')';
        }
        return $result ?: 'N/A';
    }

    // Scopes
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('result_status', $status);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('test_category', $category);
    }

    public function scopePending($query)
    {
        return $query->where('test_status', '!=', 'completed');
    }

    public function scopeAbnormal($query)
    {
        return $query->whereIn('result_status', ['abnormal_high', 'abnormal_low', 'critical_high', 'critical_low']);
    }

    public function scopeUrgent($query)
    {
        return $query->where('urgent', true);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('test_ordered_date_time', [$startDate, $endDate]);
    }

    // Helper Methods
    public function isAbnormal(): bool
    {
        return in_array($this->result_status, ['abnormal_high', 'abnormal_low', 'critical_high', 'critical_low']);
    }

    public function isCritical(): bool
    {
        return in_array($this->result_status, ['critical_high', 'critical_low']);
    }

    public function isCompleted(): bool
    {
        return $this->test_status === 'completed';
    }

    public function requiresFollowUp(): bool
    {
        return $this->requires_follow_up;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->result_status) {
            'normal' => 'success',
            'abnormal_high', 'abnormal_low' => 'warning',
            'critical_high', 'critical_low' => 'danger',
            'pending' => 'secondary',
            'inconclusive' => 'info',
            default => 'secondary'
        };
    }

    public function getTestStatusBadgeClass(): string
    {
        return match($this->test_status) {
            'completed' => 'success',
            'in_progress' => 'primary',
            'pending' => 'secondary',
            'cancelled' => 'warning',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function getResultStatusBadgeClass(): string
    {
        return match($this->result_status) {
            'normal' => 'success',
            'abnormal_high', 'abnormal_low' => 'warning',
            'critical_high', 'critical_low' => 'danger',
            'pending' => 'secondary',
            'inconclusive' => 'info',
            default => 'secondary'
        };
    }
}
