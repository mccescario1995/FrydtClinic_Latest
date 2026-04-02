<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'attending_provider_id',
        'delivering_provider_id',
        'anesthesiologist_id',
        'admission_date_time',
        'labor_onset_date_time',
        'rupture_of_membranes_date_time',
        'rupture_of_membranes_type',
        'delivery_date_time',
        'delivery_type',
        'delivery_place',
        'gravida',
        'para',
        'living_children',
        'prenatal_history',
        'risk_factors',
        'labor_duration_hours',
        'labor_duration_minutes',
        'labor_progress',
        'labor_complications',
        'presentation',
        'position',
        'episiotomy_performed',
        'episiotomy_degree',
        'perineal_tear',
        'delivery_complications',
        'anesthesia_type',
        'anesthesia_notes',
        'newborn_gender',
        'newborn_weight',
        'newborn_length',
        'newborn_apgar_1min',
        'newborn_apgar_5min',
        'newborn_apgar_10min',
        'newborn_condition',
        'newborn_complications',
        'placenta_delivery',
        'placenta_complete',
        'placenta_notes',
        'estimated_blood_loss',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate',
        'temperature',
        'postpartum_care',
        'medications_administered',
        'breastfeeding_initiation',
        'expected_discharge_date',
        'discharge_instructions',
        'follow_up_instructions',
        'delivery_summary',
        'additional_notes',
        'quality_indicators',
    ];

    protected $casts = [
        'admission_date_time' => 'datetime',
        'labor_onset_date_time' => 'datetime',
        'rupture_of_membranes_date_time' => 'datetime',
        'delivery_date_time' => 'datetime',
        'expected_discharge_date' => 'date',
        'episiotomy_performed' => 'boolean',
        'placenta_complete' => 'boolean',
        'newborn_weight' => 'decimal:2',
        'newborn_length' => 'decimal:1',
        'temperature' => 'decimal:1',
        'quality_indicators' => 'array',
    ];

    /**
     * Get the patient that owns the delivery record.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the attending provider.
     */
    public function attendingProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attending_provider_id');
    }

    /**
     * Get the delivering provider.
     */
    public function deliveringProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivering_provider_id');
    }

    /**
     * Get the anesthesiologist.
     */
    public function anesthesiologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anesthesiologist_id');
    }
}
