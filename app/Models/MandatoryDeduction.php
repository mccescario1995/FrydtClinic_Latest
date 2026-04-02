<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MandatoryDeduction extends Model
{
    protected $table = 'mandatory_deductions';

    protected $fillable = [
        'deduction_type',
        'name',
        'description',
        'percentage_rate',
        'fixed_amount',
        'minimum_base_salary',
        'maximum_deduction',
        'is_active',
        'effective_date',
        'notes',
    ];

    protected $casts = [
        'percentage_rate' => 'decimal:4',
        'fixed_amount' => 'decimal:2',
        'minimum_base_salary' => 'decimal:2',
        'maximum_deduction' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_date' => 'date',
    ];

    // Relationships
    public function employeeDeductions(): HasMany
    {
        return $this->hasMany(EmployeeDeduction::class, 'deduction_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('deduction_type', $type);
    }

    // Helper Methods
    public function calculateDeduction($baseSalary)
    {
        if (!$this->is_active || $baseSalary < $this->minimum_base_salary) {
            return 0;
        }

        $calculatedAmount = 0;

        // Calculate percentage-based deduction
        if ($this->percentage_rate > 0) {
            $calculatedAmount += ($baseSalary * $this->percentage_rate) / 100;
        }

        // Add fixed amount
        if ($this->fixed_amount > 0) {
            $calculatedAmount += $this->fixed_amount;
        }

        // Apply maximum limit if set
        if ($this->maximum_deduction > 0 && $calculatedAmount > $this->maximum_deduction) {
            $calculatedAmount = $this->maximum_deduction;
        }

        return round($calculatedAmount, 2);
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?: ucfirst(str_replace('_', ' ', $this->deduction_type));
    }

    // Philippine Deduction Types Constants
    const SSS = 'sss';
    const PHILHEALTH = 'philhealth';
    const PAGIBIG = 'pagibig';
    const TAX = 'tax';
    const OTHER = 'other';

    public static function getPhilippineDeductionTypes()
    {
        return [
            self::SSS => 'SSS (Social Security System)',
            self::PHILHEALTH => 'PhilHealth',
            self::PAGIBIG => 'Pag-IBIG (HDMF)',
            self::TAX => 'Withholding Tax',
            self::OTHER => 'Other Deductions',
        ];
    }
}
