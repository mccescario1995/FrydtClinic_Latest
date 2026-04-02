<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrenatalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'attending_physician_id',
        'midwife_id',
        'visit_date',
        'visit_time',
        'times_visited',
        'last_menstrual_period',
        'estimated_due_date',
        'gestational_age_weeks',
        'gestational_age_days',
        'gravida',
        'para',
        'abortion',
        'living_children',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'weight_kg',
        'height_cm',
        'bmi',
        'pulse_rate',
        'respiratory_rate',
        'temperature_celsius',
        'fetal_heart_rate',
        'fetal_position',
        'fetal_presentation',
        'fundal_height_cm',
        'blood_type',
        'hemoglobin_level',
        'hematocrit_level',
        'urinalysis',
        'vdrl_test',
        'hbsag_test',
        'risk_factors',
        'complications',
        'risk_level',
        'td_vaccine_given',
        'td_vaccine_date',
        'td_vaccine_dose',
        'medications',
        'iron_supplements',
        'calcium_supplements',
        'vitamin_supplements',
        'counseling_topics',
        'patient_education',
        'next_visit_date',
        'next_visit_notes',
        'pregnancy_status',
        'general_notes',
        'physician_notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime',
        'last_menstrual_period' => 'date',
        'estimated_due_date' => 'date',
        'next_visit_date' => 'date',
        'td_vaccine_given' => 'boolean',
        'td_vaccine_date' => 'date',
        'iron_supplements' => 'boolean',
        'calcium_supplements' => 'boolean',
        'vitamin_supplements' => 'boolean',
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function attendingPhysician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attending_physician_id');
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(User::class, 'midwife_id');
    }

    // Accessors & Mutators
    public function getGestationalAgeAttribute(): string
    {
        return $this->gestational_age_weeks . ' weeks ' . $this->gestational_age_days . ' days';
    }

    public function getBloodPressureAttribute(): string
    {
        return $this->blood_pressure_systolic . '/' . $this->blood_pressure_diastolic;
    }

    // Scopes
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('visit_date', [$startDate, $endDate]);
    }

    public function scopeWithAlerts($query)
    {
        return $query->whereNotNull('alerts_flags');
    }

    // Helper Methods
    public function isHighRisk(): bool
    {
        return !empty($this->risk_factors_identified);
    }

    public function hasAlerts(): bool
    {
        return !empty($this->alerts_flags);
    }

    public function getNextVisitType(): string
    {
        return $this->next_visit_type ?? 'Routine prenatal visit';
    }
}
