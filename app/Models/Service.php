<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Service extends Model
{
    use CrudTrait;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'service_type',
        'category',
        'base_price',
        'philhealth_price',
        'philhealth_covered',
        'discount_percentage',
        'duration_minutes',
        'preparation_instructions',
        'post_service_instructions',
        'contraindications',
        'required_equipment',
        'required_supplies',
        'staff_requirements',
        'status',
        'requires_appointment',
        'available_emergency',
        'requires_lab_results',
        'advance_booking_days',
        'available_days',
        'start_time',
        'end_time',
        'quality_indicators',
        'regulatory_requirements',
        'last_review_date',
        'next_review_date',
        'consent_form_required',
        'documentation_requirements',
        'internal_notes',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'philhealth_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'philhealth_covered' => 'boolean',
        'requires_appointment' => 'boolean',
        'available_emergency' => 'boolean',
        'requires_lab_results' => 'boolean',
        'available_days' => 'array',
        'last_review_date' => 'date',
        'next_review_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function paymentItems(): HasMany
    {
        return $this->hasMany(PaymentItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function getDisplayPriceAttribute()
    {
        return '₱' . number_format($this->base_price, 2);
    }

    public function getEffectivePriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->base_price * (1 - ($this->discount_percentage / 100));
        }
        return $this->base_price;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function requiresAppointment()
    {
        return $this->requires_appointment;
    }
}
