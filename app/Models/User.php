<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Appointment;
use App\Models\EmployeeSchedule;
use App\Models\EmployeeProfile;
use App\Models\PatientProfile;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use CrudTrait;
    use HasRoles;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'status',
        'pin',
        'pin_verified',
        'pin_verified_at',
        'otp_code',
        'otp_expires_at',
        'otp_verified_at',
        'otp_attempts',
        'otp_last_sent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'pin_verified_at'   => 'datetime',
            'otp_expires_at'    => 'datetime',
            'otp_verified_at'   => 'datetime',
            'otp_last_sent_at'  => 'datetime',
        ];
    }

    // Helper methods for user type checking
    public function isPatient()
    {
        return $this->user_type === 'patient';
    }

    public function isEmployee()
    {
        return $this->user_type === 'employee';
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has a PIN set
     */
    public function hasPin()
    {
        return !is_null($this->pin);
    }

    /**
     * Check if PIN is verified for current session
     */
    public function isPinVerified()
    {
        return $this->pin_verified;
    }

    /**
     * Verify PIN
     */
    public function verifyPin($pin)
    {
        if ($this->pin == $pin) {
            $this->update([
                'pin_verified' => true,
                'pin_verified_at' => now(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Set PIN
     */
    public function setPin($pin)
    {
        $this->update([
            'pin' => $pin,
            'pin_verified' => false,
            'pin_verified_at' => null,
        ]);
    }

    /**
     * Mark PIN as verified
     */
    public function markPinAsVerified()
    {
        $this->update([
            'pin_verified' => true,
            'pin_verified_at' => now(),
        ]);
    }

    /**
     * Reset PIN verification (for logout)
     */
    public function resetPinVerification()
    {
        $this->update([
            'pin_verified' => false,
            'pin_verified_at' => null,
        ]);
    }

    /**
     * Generate OTP code
     */
    public function generateOtp()
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5), // 5 minutes expiry
            'otp_attempts' => 0,
            'otp_last_sent_at' => now(),
        ]);

        return $otp;
    }

    /**
     * Send OTP via SMS
     */
    public function sendOtp()
    {
        // Get phone number from patient profile if user is patient
        $phone = null;
        if ($this->user_type === 'patient' && $this->patientProfile) {
            $phone = $this->patientProfile->phone;
        } elseif ($this->user_type === 'employee' && $this->employeeProfile) {
            $phone = $this->employeeProfile->phone;
        }

        if (!$phone || $phone === 'Not provided') {
            return false;
        }

        $smsService = app(\App\Services\SmsService::class);
        return $smsService->sendOtp($phone, [
            'user_id' => $this->id,
            'type' => 'otp_verification'
        ]);
    }

    /**
     * Verify OTP code via iProgSMS API
     */
    public function verifyOtp($otp)
    {
        // Get phone number from patient profile if user is patient
        $phone = null;
        if ($this->user_type === 'patient' && $this->patientProfile) {
            $phone = $this->patientProfile->phone;
        } elseif ($this->user_type === 'employee' && $this->employeeProfile) {
            $phone = $this->employeeProfile->phone;
        }

        if (!$phone || $phone === 'Not provided') {
            Log::warning('OTP verification failed - no phone number', [
                'user_id' => $this->id,
                'user_type' => $this->user_type,
            ]);
            return false;
        }

        $smsService = app(\App\Services\SmsService::class);
        $result = $smsService->verifyOtp($phone, $otp, $this->id);

        // Add debugging info
        if (!$result) {
            Log::warning('OTP verification via service failed', [
                'user_id' => $this->id,
                'provided_otp' => $otp,
                'stored_otp' => $this->otp_code,
                'otp_match' => ($this->otp_code === $otp),
                'provided_otp_length' => strlen((string)$otp),
                'stored_otp_length' => strlen((string)$this->otp_code),
                'otp_expires_at' => $this->otp_expires_at,
                'otp_attempts' => $this->otp_attempts,
            ]);
        }

        return $result;
    }

    /**
     * Check if OTP is expired
     */
    public function isOtpExpired()
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }

    /**
     * Check if OTP can be resent (rate limiting)
     */
    public function canResendOtp()
    {
        return !$this->otp_last_sent_at || $this->otp_last_sent_at->addSeconds(30)->isPast();
    }

    /**
     * Clear OTP data
     */
    public function clearOtp()
    {
        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_verified_at' => null,
            'otp_attempts' => 0,
            'otp_last_sent_at' => null,
        ]);
    }

    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

// A user (as employee) can have many appointments
    public function employeeAppointments()
    {
        return $this->hasMany(Appointment::class, 'employee_id');
    }

// A user (as employee) can have schedules
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_id');
    }

// Patient profile relationship (already exists)
    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class);
    }

// Employee profile relationship
    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class, 'employee_id');
    }

    // Employee deductions relationship
    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'employee_id');
    }
}
