<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostpartumRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'provider_id',
        'visit_number',
        'visit_date',
        'weeks_postpartum',
        'days_postpartum',
        'weight',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'general_condition',
        'breast_condition',
        'uterus_condition',
        'perineum_condition',
        'lochia_condition',
        'episiotomy_condition',
        'mood_assessment',
        'emotional_support_needs',
        'postpartum_depression_screening',
        'mental_health_notes',
        'breastfeeding_status',
        'breastfeeding_challenges',
        'lactation_support',
        'infant_feeding_assessment',
        'infant_care_education',
        'contraceptive_method',
        'family_planning_counseling',
        'next_contraceptive_visit',
        'postpartum_complications',
        'medications_prescribed',
        'wound_care_instructions',
        'activity_restrictions',
        'follow_up_date',
        'follow_up_reason',
        'education_provided',
        'nutrition_counseling',
        'exercise_guidance',
        'warning_signs_education',
        'assessment_notes',
        'plan_notes',
        'alerts_flags',
        'quality_indicators_met',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'next_contraceptive_visit' => 'date',
        'follow_up_date' => 'date',
        'education_provided' => 'array',
        'alerts_flags' => 'array',
        'quality_indicators_met' => 'array',
        'postpartum_depression_screening' => 'boolean',
        'infant_feeding_assessment' => 'boolean',
        'weight' => 'decimal:2',
        'temperature' => 'decimal:1',
    ];

    /**
     * Get the patient that owns the postpartum record.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the provider that created the postpartum record.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
