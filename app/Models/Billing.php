<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Billing extends Model
{
    use CrudTrait;
    protected $table = 'billing';

    protected $fillable = [
        'patient_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'billing_description',
        'subtotal_amount',
        'discount_amount',
        'philhealth_coverage',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'payment_status',
        'payment_method',
        'payment_reference',
        'has_insurance',
        'insurance_provider',
        'insurance_policy_number',
        'insurance_coverage_amount',
        'philhealth_member',
        'philhealth_number',
        'philhealth_benefit_amount',
        'services_rendered',
        'service_start_date',
        'service_end_date',
        'responsible_party_name',
        'responsible_party_relationship',
        'responsible_party_contact',
        'requires_follow_up',
        'follow_up_date',
        'billing_notes',
        'collection_notes',
        'created_by',
        'last_updated_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'service_start_date' => 'date',
        'service_end_date' => 'date',
        'follow_up_date' => 'date',
        'services_rendered' => 'array',
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'philhealth_coverage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'insurance_coverage_amount' => 'decimal:2',
        'philhealth_benefit_amount' => 'decimal:2',
    ];

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'overdue');
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // Helper Methods
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->payment_status === 'overdue' ||
               ($this->due_date && $this->due_date->isPast() && $this->payment_status !== 'paid');
    }

    public function getOutstandingAmount(): float
    {
        return $this->balance_due ?? $this->total_amount;
    }

    /**
     * Calculate total amount based on subtotal, discounts, and taxes
     */
    public function calculateTotalAmount(): float
    {
        return $this->subtotal_amount - $this->discount_amount - $this->philhealth_coverage + $this->tax_amount;
    }

    /**
     * Calculate balance due
     */
    public function calculateBalanceDue(): float
    {
        return max(0, $this->calculateTotalAmount() - $this->amount_paid);
    }

    /**
     * Update payment status based on amounts
     */
    public function updatePaymentStatus(): void
    {
        $totalAmount = $this->calculateTotalAmount();
        $balanceDue = $this->calculateBalanceDue();

        if ($balanceDue <= 0) {
            $this->payment_status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        // Check if overdue
        if ($this->due_date && $this->due_date->isPast() && $balanceDue > 0) {
            $this->payment_status = 'overdue';
        }

        $this->total_amount = $totalAmount;
        $this->balance_due = $balanceDue;
        $this->save();
    }

    /**
     * Apply PhilHealth coverage based on membership
     */
    public function applyPhilHealthCoverage(): void
    {
        if ($this->philhealth_member && $this->philhealth_number) {
            // Calculate PhilHealth benefit (typically 45% of the first ₱10,000)
            $philhealthBase = min($this->subtotal_amount, 10000); // ₱10,000 cap
            $this->philhealth_benefit_amount = $philhealthBase * 0.45; // 45% coverage
            $this->philhealth_coverage = $this->philhealth_benefit_amount;
        } else {
            $this->philhealth_coverage = 0;
            $this->philhealth_benefit_amount = 0;
        }

        $this->updatePaymentStatus();
    }

    /**
     * Add payment to billing record
     */
    public function addPayment($amount, $paymentMethod = null, $reference = null): void
    {
        $this->amount_paid += $amount;

        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
        }

        if ($reference) {
            $this->payment_reference = $reference;
        }

        $this->updatePaymentStatus();
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return '₱' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted balance due
     */
    public function getFormattedBalanceDueAttribute(): string
    {
        return '₱' . number_format($this->balance_due, 2);
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        return '₱' . number_format($this->amount_paid, 2);
    }
}
