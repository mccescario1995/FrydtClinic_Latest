<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use CrudTrait;
    use HasFactory;
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'employee_id',
        'service_id',
        'appointment_datetime',
        'end_time',
        'duration_in_minutes',
        'status',
        'purpose',
        'type',
        'patient_notes',
        'employee_notes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'appointment_datetime' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the patient that owns the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the employee that the appointment is with.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the service for the appointment.
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Get the payments for the appointment.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'appointment_id');
    }

}
