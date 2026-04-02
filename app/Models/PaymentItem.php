<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class PaymentItem extends Model
{
    use CrudTrait;

    protected $fillable = [
        'payment_id',
        'service_id',
        'service_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '₱' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '₱' . number_format($this->total_price, 2);
    }
}
