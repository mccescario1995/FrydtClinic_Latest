<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDeduction extends Model
{
    protected $table = 'employee_deductions';

    protected $fillable = [
        'employee_id',
        'deduction_id',
        'custom_percentage_rate',
        'custom_fixed_amount',
        'is_enabled',
        'notes',
    ];

    protected $casts = [
        'custom_percentage_rate' => 'decimal:4',
        'custom_fixed_amount' => 'decimal:2',
        'is_enabled' => 'boolean',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(MandatoryDeduction::class, 'deduction_id');
    }

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByDeductionType($query, $type)
    {
        return $query->whereHas('deduction', function ($q) use ($type) {
            $q->where('deduction_type', $type);
        });
    }

    // Helper Methods
    public function getEffectivePercentageRate()
    {
        return $this->custom_percentage_rate !== null ? $this->custom_percentage_rate : $this->deduction->percentage_rate;
    }

    public function getEffectiveFixedAmount()
    {
        return $this->custom_fixed_amount !== null ? $this->custom_fixed_amount : $this->deduction->fixed_amount;
    }

    public function calculateDeduction($baseSalary)
    {
        if (!$this->is_enabled || !$this->deduction->is_active || $baseSalary < $this->deduction->minimum_base_salary) {
            return 0;
        }

        $percentageRate = $this->getEffectivePercentageRate();
        $fixedAmount = $this->getEffectiveFixedAmount();

        $calculatedAmount = 0;

        // Calculate percentage-based deduction
        if ($percentageRate > 0) {
            $calculatedAmount += ($baseSalary * $percentageRate) / 100;
        }

        // Add fixed amount
        if ($fixedAmount > 0) {
            $calculatedAmount += $fixedAmount;
        }

        // Apply maximum limit if set
        if ($this->deduction->maximum_deduction > 0 && $calculatedAmount > $this->deduction->maximum_deduction) {
            $calculatedAmount = $this->deduction->maximum_deduction;
        }

        return round($calculatedAmount, 2);
    }
}
