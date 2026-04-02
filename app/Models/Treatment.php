<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $table = 'treatments';

    protected $fillable = [
        'patient_id',
        'prescribed_by',
        'administered_by',
        'treatment_type',
        'treatment_name',
        'treatment_description',
        'generic_name',
        'brand_name',
        'dosage',
        'frequency',
        'route',
        'duration_days',
        'quantity_prescribed',
        'prescribed_date',
        'start_date',
        'end_date',
        'administered_date',
        'administration_site',
        'status',
        'indication',
        'treatment_goal',
        'patient_response',
        'response_notes',
        'side_effects',
        'adverse_reactions',
        'complications',
        'monitoring_parameters',
        'next_follow_up_date',
        'follow_up_instructions',
        'unit_cost',
        'total_cost',
        'covered_by_philhealth',
        'special_instructions',
        'nursing_notes',
        'physician_notes',
        'priority',
        'requires_monitoring',
    ];

    protected $casts = [
        'prescribed_date' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'administered_date' => 'datetime',
        'next_follow_up_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'covered_by_philhealth' => 'boolean',
        'requires_monitoring' => 'boolean',
        'quantity_prescribed' => 'integer',
        'duration_days' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function prescriber()
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }

    public function administrator()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['prescribed', 'started', 'ongoing']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('treatment_type', $type);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getIsActiveAttribute()
    {
        return in_array($this->status, ['prescribed', 'started', 'ongoing']);
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->end_date) return false;
        return $this->end_date->isPast();
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) return null;
        return now()->diffInDays($this->end_date, false);
    }

    public function getFormattedDosageAttribute()
    {
        $parts = [];
        if ($this->dosage) $parts[] = $this->dosage;
        if ($this->frequency) $parts[] = $this->frequency;
        if ($this->route) $parts[] = $this->route;

        return implode(' ', $parts);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    public function markAsStarted()
    {
        $this->update([
            'status' => 'started',
            'start_date' => now(),
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'end_date' => now(),
        ]);
    }

    public function markAsDiscontinued()
    {
        $this->update([
            'status' => 'discontinued',
            'end_date' => now(),
        ]);
    }

    public function calculateTotalCost()
    {
        if ($this->unit_cost && $this->quantity_prescribed) {
            $this->total_cost = $this->unit_cost * $this->quantity_prescribed;
            $this->save();
        }
    }
}
