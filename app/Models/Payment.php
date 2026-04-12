<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Payment extends Model
{
    use CrudTrait;
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'prescription_id',
        'payment_reference',
        'paypal_payment_id',
        'paypal_payer_id',
        'paypal_order_id',
        'gcash_qr_data',
        'gcash_reference',
        'proof_of_payment_path',
        'proof_of_payment_notes',
        'proof_uploaded_at',
        'approved_by',
        'approved_at',
        'paid_amount',
        'remaining_balance',
        'is_partial_payment',
        'payment_history',
        'amount',
        'currency',
        'payment_method',
        'status',
        'description',
        'paypal_response',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'paypal_response' => 'array',
        'payment_history' => 'array',
        'paid_at' => 'datetime',
        'proof_uploaded_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_partial_payment' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = 'PAY-' . strtoupper(uniqid());
            }
        });

        // Create billing record when payment status changes to successful or completed
        static::updating(function ($payment) {
            if ($payment->isDirty('status') &&
                in_array($payment->status, ['successful', 'completed']) &&
                !in_array($payment->getOriginal('status'), ['successful', 'completed'])) {
                // Create billing record for successful/completed payments
                $payment->createBillingRecord(1); // Use admin ID 1 as default
            }
        });
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function billingRecords()
    {
        return $this->hasMany(\App\Models\Billing::class, 'payment_reference', 'payment_reference');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAwaitingApproval(): bool
    {
        return $this->status === 'awaiting_approval';
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    public function hasProofOfPayment(): bool
    {
        return !empty($this->proof_of_payment_path);
    }

    public function isFullyPaid(): bool
    {
        return $this->remaining_balance <= 0;
    }

    public function isPartialPayment(): bool
    {
        return $this->is_partial_payment;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    public function markAsSuccessful(): void
    {
        $this->update([
            'status' => 'successful',
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function markAsAwaitingApproval(): void
    {
        $this->update(['status' => 'awaiting_approval']);
    }

    public function approve($approvedByUserId, $notes = null): void
    {
        $this->update([
            'status' => 'successful',
            'approved_by' => $approvedByUserId,
            'approved_at' => now(),
            'paid_at' => now(),
            'paid_amount' => $this->paid_amount ?: $this->amount,
            'remaining_balance' => 0,
            'notes' => $notes,
        ]);

        // Update prescription status if applicable
        if ($this->prescription) {
            $this->prescription->update(['status' => 'fully_dispensed']);
        }

        // Create billing record when payment is approved
        $this->createBillingRecord($approvedByUserId);
    }

    public function reject($notes = null): void
    {
        $this->update([
            'status' => 'failed',
            'notes' => $notes,
        ]);

        // Reset prescription status if applicable
        if ($this->prescription) {
            $this->prescription->update(['status' => 'fully_dispensed']);
        }
    }

    public function uploadProofOfPayment($filePath, $notes = null): void
    {
        $this->update([
            'proof_of_payment_path' => $filePath,
            'proof_of_payment_notes' => $notes,
            'proof_uploaded_at' => now(),
            'status' => 'awaiting_approval',
        ]);
    }

    public function processPartialPayment($paidAmount): void
    {
        $newPaidAmount = $this->paid_amount + $paidAmount;
        $newRemainingBalance = max(0, $this->amount - $newPaidAmount);

        // For GCash payments, always require admin approval
        $newStatus = $this->payment_method === 'gcash' ? 'awaiting_approval' :
                    ($newRemainingBalance <= 0 ? 'successful' : 'awaiting_approval');

        $this->update([
            'paid_amount' => $newPaidAmount,
            'remaining_balance' => $newRemainingBalance,
            'is_partial_payment' => $newRemainingBalance > 0,
            'status' => $newStatus,
        ]);

        // Update billing records if payment is successful
        if ($newStatus === 'successful') {
            $this->updateBillingRecords();
        }
    }

    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return '₱' . number_format($this->paid_amount, 2);
    }

    public function getRemainingBalanceAttribute($value): float
    {
        // If remaining_balance is null (for existing records), default to amount
        return (float) ($value ?? $this->amount ?? 0.0);
    }
    public function getFormattedRemainingBalanceAttribute(): string
    {
        return '₱' . number_format($this->remaining_balance, 2);
    }

    /**
     * Create a billing record when payment is approved
     */
    public function createBillingRecord($createdByUserId): void
    {
        // Check if billing record already exists for this payment
        $existingBilling = \App\Models\Billing::where('payment_reference', $this->payment_reference)->first();
        if ($existingBilling) {
            return; // Billing record already exists
        }

        // Prepare services rendered data
        $servicesRendered = []; 


        if ($this->appointment) {
            $servicesRendered[] = [
                'name' => $this->appointment->service->name ?? 'Medical Service',
                'date' => $this->appointment->appointment_datetime->format('Y-m-d'),
                'provider' => $this->appointment->employee->name ?? 'N/A',
                'amount' => $this->amount,
            ];
        } elseif ($this->prescription) {
            $servicesRendered[] = [
                'name' => $this->prescription->inventory->name ?? 'Prescription Medicine',
                'date' => $this->prescription->dispensed_date ? $this->prescription->dispensed_date->format('Y-m-d') : now()->format('Y-m-d'),
                'provider' => $this->prescription->dispenser->name ?? 'N/A',
                'amount' => $this->amount,
                'quantity' => $this->prescription->quantity_dispensed,
            ];
        } elseif ($this->items->count() > 0) {
            foreach ($this->items as $item) {
                $servicesRendered[] = [
                    'name' => $item->service_name,
                    'date' => now()->format('Y-m-d'),
                    'provider' => 'N/A',
                    'amount' => $item->total_price,
                ];
            }
        }

        // Get patient PhilHealth information
        $patientProfile = $this->patient->patientProfile ?? null;
        $philhealthMember = $patientProfile ? $patientProfile->isPhilHealthMember() : false;
        $philhealthNumber = $patientProfile ? $patientProfile->philhealth_number : null;

        // Create billing record
        $billing = \App\Models\Billing::create([
            'patient_id' => $this->patient_id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30), // 30 days payment term
            'billing_description' => $this->description ?? 'Medical services payment',
            'subtotal_amount' => $this->amount,
            'discount_amount' => 0, // Could be calculated based on business rules
            'philhealth_coverage' => 0, // Will be calculated by applyPhilHealthCoverage
            'tax_amount' => 0, // Could be calculated based on tax rules
            'amount_paid' => $this->paid_amount,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'philhealth_member' => $philhealthMember,
            'philhealth_number' => $philhealthNumber,
            'services_rendered' => $servicesRendered,
            'service_start_date' => $this->appointment ? $this->appointment->appointment_datetime->toDateString() : ($this->prescription ? $this->prescription->dispensed_date->toDateString() : now()->toDateString()),
            'service_end_date' => $this->appointment ? $this->appointment->end_time->toDateString() : ($this->prescription ? $this->prescription->dispensed_date->toDateString() : now()->toDateString()),
            'created_by' => $createdByUserId,
            'billing_notes' => 'Automatically generated from payment approval',
        ]);

        // Apply PhilHealth coverage and update payment status
        $billing->applyPhilHealthCoverage();
    }

    /**
     * Update existing billing records when payment status changes
     */
    public function updateBillingRecords(): void
    {
        // Find billing records that might be related to this payment
        $billingRecords = \App\Models\Billing::where('patient_id', $this->patient_id)
            ->where(function($query) {
                $query->where('payment_reference', $this->payment_reference)
                      ->orWhere('services_rendered', 'like', '%' . ($this->appointment?->service->name ?? '') . '%')
                      ->orWhere('services_rendered', 'like', '%' . ($this->prescription?->inventory->name ?? '') . '%');
            })
            ->get();

        foreach ($billingRecords as $billing) {
            // Add this payment amount to the billing record
            $billing->addPayment($this->amount, $this->payment_method, $this->payment_reference);
        }
    }

}
