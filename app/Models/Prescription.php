<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescriptions';

    protected $fillable = [
        'treatment_id',
        'inventory_id',
        'prescribed_by',
        'dispensed_by',
        'prescription_number',
        'quantity_prescribed',
        'quantity_dispensed',
        'dosage_instructions',
        'duration_days',
        'special_instructions',
        'prescribed_date',
        'dispensed_date',
        'unit_price',
        'total_price',
        'covered_by_philhealth',
        'status',
        'purchase_location',
        'pharmacist_notes',
        'physician_notes',
    ];

    protected $casts = [
        'prescribed_date' => 'datetime',
        'dispensed_date' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'covered_by_philhealth' => 'boolean',
        'quantity_prescribed' => 'integer',
        'quantity_dispensed' => 'integer',
        'duration_days' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            if (empty($prescription->prescription_number)) {
                $prescription->prescription_number = static::generatePrescriptionNumber();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function prescriber()
    {
        return $this->belongsTo(User::class, 'prescribed_by');
    }

    public function dispenser()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function patient()
    {
        return $this->hasOneThrough(User::class, Treatment::class, 'id', 'id', 'treatment_id', 'patient_id');
    }

    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['prescribed', 'partially_dispensed', 'pending_payment']);
    }

    public function scopeDispensed($query)
    {
        return $query->whereIn('status', ['fully_dispensed', 'pending_payment']);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->whereHas('treatment', function ($q) use ($patientId) {
            $q->where('patient_id', $patientId);
        });
    }

    public function scopeByInventory($query, $inventoryId)
    {
        return $query->where('inventory_id', $inventoryId);
    }

    public function scopePendingDispensing($query)
    {
        return $query->where('quantity_dispensed', '<', $this->quantity_prescribed);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getIsFullyDispensedAttribute()
    {
        return $this->status === 'fully_dispensed';
    }

    public function getIsPartiallyDispensedAttribute()
    {
        return $this->status === 'partially_dispensed';
    }

    public function getIsPendingPaymentAttribute()
    {
        return $this->status === 'pending_payment';
    }

    public function getIsExternalPurchaseAttribute()
    {
        return $this->status === 'external_purchase';
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity_prescribed - $this->quantity_dispensed;
    }

    public function getCanDispenseAttribute()
    {
        return $this->remaining_quantity > 0 && $this->inventory->current_quantity > 0;
    }

    public function getFormattedPriceAttribute()
    {
        return $this->total_price ? '₱' . number_format($this->total_price, 2) : 'N/A';
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    public static function generatePrescriptionNumber()
    {
        do {
            $number = 'RX-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (static::where('prescription_number', $number)->exists());

        return $number;
    }

    public function dispense($quantity, $dispensedBy = null, $notes = null)
    {
        // Validate stock availability
        if ($this->inventory->current_quantity < $quantity) {
            throw new \Exception('Insufficient stock for dispensing');
        }

        // Check if already fully dispensed
        if ($this->isFullyDispensed) {
            throw new \Exception('Prescription is already fully dispensed');
        }

        $newDispensedQuantity = $this->quantity_dispensed + $quantity;

        // Validate not exceeding prescribed quantity
        if ($newDispensedQuantity > $this->quantity_prescribed) {
            throw new \Exception('Cannot dispense more than prescribed quantity');
        }

        // Update prescription
        $this->quantity_dispensed = $newDispensedQuantity;
        if ($dispensedBy !== null) {
            $this->dispensed_by = $dispensedBy;
        }
        $this->dispensed_date = now();

        if ($newDispensedQuantity == $this->quantity_prescribed) {
            $this->status = 'fully_dispensed';
        } else {
            $this->status = 'partially_dispensed';
        }

        if ($notes) {
            $this->pharmacist_notes = $notes;
        }

        // Calculate pricing if not set
        if (!$this->unit_price && $this->inventory->selling_price) {
            $this->unit_price = $this->inventory->selling_price;
            $this->total_price = $this->unit_price * $quantity;
        }

        $this->save();

        // Update inventory stock
        $this->inventory->removeStock($quantity, "Prescription #{$this->prescription_number} - {$quantity} units dispensed");

        return $this;
    }

    public function cancel($reason = null)
    {
        if ($this->status === 'cancelled') {
            throw new \Exception('Prescription is already cancelled');
        }

        $this->status = 'cancelled';
        if ($reason) {
            $this->pharmacist_notes = ($this->pharmacist_notes ? $this->pharmacist_notes . ' | ' : '') . 'CANCELLED: ' . $reason;
        }
        $this->save();

        return $this;
    }

    public function calculateTotalPrice()
    {
        if ($this->unit_price && $this->quantity_dispensed) {
            $this->total_price = $this->unit_price * $this->quantity_dispensed;
            $this->save();
        }
    }
}
