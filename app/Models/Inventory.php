<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'item_code',
        'description',
        'manufacturer',
        'model_number',
        'serial_number',
        'item_type',
        'category',
        'current_quantity',
        'minimum_quantity',
        'maximum_quantity',
        'unit_of_measure',
        'storage_location',
        'room_number',
        'cabinet_drawer',
        'storage_conditions',
        'unit_cost',
        'selling_price',
        'supplier_name',
        'supplier_contact',
        'expiry_date',
        'batch_lot_number',
        'fda_registration_number',
        'requires_prescription',
        'regulatory_notes',
        'status',
        'last_inventory_check',
        'next_maintenance_date',
        'maintenance_notes',
        'usage_count',
        'last_used_date',
        'usage_notes',
        'low_stock_alert',
        'expiry_alert',
        'alert_before_expiry_days',
        'manual_document_path',
        'special_handling_instructions',
        'internal_notes',
    ];

    protected $casts = [
        'current_quantity' => 'integer',
        'minimum_quantity' => 'integer',
        'maximum_quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'expiry_date' => 'date',
        'last_inventory_check' => 'date',
        'next_maintenance_date' => 'date',
        'last_used_date' => 'date',
        'requires_prescription' => 'boolean',
        'low_stock_alert' => 'boolean',
        'expiry_alert' => 'boolean',
        'alert_before_expiry_days' => 'integer',
        'usage_count' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function movements()
    {
        return $this->hasMany(InventoryMovements::class, 'inventory_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_quantity <= minimum_quantity');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('item_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getIsLowStockAttribute()
    {
        return $this->current_quantity <= $this->minimum_quantity;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute()
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->diffInDays(now()) <= $this->alert_before_expiry_days;
    }

    public function getStockStatusAttribute()
    {
        if ($this->isLowStock) return 'low_stock';
        if ($this->current_quantity == 0) return 'out_of_stock';
        if ($this->maximum_quantity && $this->current_quantity >= $this->maximum_quantity) return 'overstock';
        return 'normal';
    }

    public function getFormattedUnitCostAttribute()
    {
        return $this->unit_cost ? '₱' . number_format($this->unit_cost, 2) : 'N/A';
    }

    public function getFormattedSellingPriceAttribute()
    {
        return $this->selling_price ? '₱' . number_format($this->selling_price, 2) : 'N/A';
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) return 'N/A';

        $days = now()->diffInDays($this->expiry_date, false); // false to get negative for past dates

        return $days < 0 ? 'Expired' : $days;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    public function updateStock($quantity, $type = 'adjustment', $notes = null)
    {
        $oldQuantity = $this->current_quantity;
        $this->current_quantity = $quantity;
        $this->save();

        // Create movement record
        InventoryMovements::create([
            'inventory_id' => $this->id,
            'movement_type' => $type,
            'quantity_moved' => $quantity - $oldQuantity,
            'previous_quantity' => $oldQuantity,
            'new_quantity' => $quantity,
            'notes' => $notes,
            'performed_by' => auth()->id(),
        ]);

        return $this;
    }

    public function addStock($quantity, $notes = null)
    {
        return $this->updateStock($this->current_quantity + $quantity, 'stock_in', $notes);
    }

    public function removeStock($quantity, $notes = null)
    {
        return $this->updateStock($this->current_quantity - $quantity, 'stock_out', $notes);
    }

    /*
    |--------------------------------------------------------------------------
    | PRESCRIPTION METHODS
    |--------------------------------------------------------------------------
    */

    public function dispenseForPrescription($quantity, Prescription $prescription, $notes = null)
    {
        // Validate prescription requirements
        if ($this->requires_prescription && !$prescription) {
            throw new \Exception('This item requires a valid prescription');
        }

        // Check stock availability
        if ($this->current_quantity < $quantity) {
            throw new \Exception('Insufficient stock for dispensing');
        }

        // Create detailed notes
        $dispenseNotes = "Prescription #{$prescription->prescription_number} - {$quantity} units dispensed";
        if ($notes) {
            $dispenseNotes .= " | {$notes}";
        }

        // Update stock
        $this->removeStock($quantity, $dispenseNotes);

        // Update usage tracking
        $this->usage_count += $quantity;
        $this->last_used_date = now();
        $this->usage_notes = $dispenseNotes;
        $this->save();

        return $this;
    }

    public function canDispense($quantity)
    {
        return $this->current_quantity >= $quantity && $this->status === 'active';
    }

    public function getAvailableStockForPrescription()
    {
        return min($this->current_quantity, $this->maximum_quantity ?: $this->current_quantity);
    }
}
