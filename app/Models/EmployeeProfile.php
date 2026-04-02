<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $table = 'employee_profiles';
    protected $guarded = ['id'];

    protected $fillable = [
        'employee_id',
        // 'employee_number',
        'position',
        'specialty',
        'hire_date',
        'gender',
        'phone',
        'address',
        'image_path',
        'pin',
        'hourly_rate',
        'employment_type',
        'status',
        'pin'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'pin' => "int"
    ];

    // RELATIONSHIPS
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_id');
    }

    public function attendance()
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(EmployeeLeave::class, 'employee_id');
    }

    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class, 'employee_id');
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isOnLeave()
    {
        return $this->status === 'on_leave';
    }

    public function getFullNameAttribute()
    {
        return $this->employee->name;
    }

    /**
     * Set the phone number with proper formatting - enforces 63xxxxxxxxx format
     */
    public function setPhoneAttribute($value)
    {
        if ($value) {
            // Remove all non-numeric characters
            $phone = preg_replace('/\D/', '', $value);

            // Validate and format Philippine phone numbers to 63xxxxxxxxx format
            if (str_starts_with($phone, '63') && strlen($phone) === 12) {
                // Already in correct format: 63xxxxxxxxx
                $this->attributes['phone'] = $phone;
            } elseif (str_starts_with($phone, '+63') && strlen($phone) === 13) {
                // Remove + from +63xxxxxxxxx to get 63xxxxxxxxx
                $this->attributes['phone'] = substr($phone, 1);
            } elseif (str_starts_with($phone, '0') && strlen($phone) === 11) {
                // Convert 0xxxxxxxxx to 63xxxxxxxxx
                $this->attributes['phone'] = '63' . substr($phone, 1);
            } elseif (strlen($phone) === 10 && str_starts_with($phone, '9')) {
                // Convert 9xxxxxxxxx to 63xxxxxxxxx
                $this->attributes['phone'] = '63' . $phone;
            } elseif (strlen($phone) === 11 && str_starts_with($phone, '9')) {
                // Handle 09xxxxxxxxx format (11 digits starting with 9 after 0)
                $this->attributes['phone'] = '63' . substr($phone, 1);
            } else {
                // For invalid formats, throw an exception to prevent saving
                throw new \InvalidArgumentException('Phone number must be a valid Philippine mobile number in format: 63xxxxxxxxx, +63xxxxxxxxx, 0xxxxxxxxx, 9xxxxxxxxx, or 09xxxxxxxxx');
            }
        } else {
            $this->attributes['phone'] = $value;
        }
    }
}
