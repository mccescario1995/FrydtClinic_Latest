<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PatientProfile extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'patient_profiles';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'address',
        'phone',
        'birth_date',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'philhealth_membership',
        'philhealth_number',
        'image_path',
        'civil_status',
        'occupation',
        'religion',
        'blood_type',
        'barangay_captain',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if patient is a confirmed PhilHealth member
     */
    public function isPhilHealthMember(): bool
    {
        return !empty($this->philhealth_membership) &&
               $this->philhealth_membership === true &&
               !empty($this->philhealth_number);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

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
