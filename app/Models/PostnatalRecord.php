<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostnatalRecord extends Model
{

    protected $table = 'postnatal_records';
    protected $fillable = [
        'patient_id',
        'provider_id',
        'visit_number',
        'visit_date',
        'days_postpartum',
        'weeks_postpartum',
        'weight',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'oxygen_saturation',
        'general_condition',
        'breast_condition',
        'uterus_condition',
        'perineum_condition',
        'lochia_condition',
        'episiotomy_condition',
        'breastfeeding_status',
        'breastfeeding_notes',
        'latch_assessment',
        'newborn_check',
        'newborn_weight',
        'newborn_notes',
        'family_planning_method',
        'family_planning_counseling',
        'chief_complaint',
        'assessment',
        'plan',
        'medications_prescribed',
        'instructions_given',
        'follow_up_date',
        'next_visit_type',
        'notes',
        'alerts_flags',
        'quality_indicators_met',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'follow_up_date' => 'date',
        'medications_prescribed' => 'array',
        'alerts_flags' => 'array',
        'quality_indicators_met' => 'array',
        'newborn_check' => 'boolean',
        'weight' => 'decimal:2',
        'temperature' => 'decimal:1',
        'newborn_weight' => 'decimal:2',
    ];

    /**
     * Get the patient that owns the postnatal record.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the provider that created the postnatal record.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
