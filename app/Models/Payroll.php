<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $table = 'employee_payroll';

    protected $fillable = [
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'total_hours_worked',
        'regular_hours',
        'overtime_hours',
        'hourly_rate',
        'overtime_rate',
        'gross_pay',
        'deductions',
        'sss_deduction',
        'philhealth_deduction',
        'pagibig_deduction',
        'tax_deduction',
        'other_deductions',
        'net_pay',
        'status',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'payment_date' => 'date',
        'total_hours_worked' => 'decimal:2',
        'regular_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'deductions' => 'decimal:2',
        'sss_deduction' => 'decimal:2',
        'philhealth_deduction' => 'decimal:2',
        'pagibig_deduction' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByPayPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('pay_period_start', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Helper Methods
    public function calculateWorkedHours()
    {
        // Calculate total hours worked from attendance records
        $attendances = EmployeeAttendance::where('employee_id', $this->employee_id)
            ->whereBetween('date', [$this->pay_period_start, $this->pay_period_end])
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->get();

        $totalHours = 0;
        foreach ($attendances as $attendance) {
            $start = \Carbon\Carbon::parse($attendance->check_in_time);
            $end = \Carbon\Carbon::parse($attendance->check_out_time);
            // Use minutes for decimal precision, then convert to hours
            $minutes = $start->diffInMinutes($end);
            $hours = $minutes / 60;
            $totalHours += $hours;
        }

        return $totalHours;
    }

    public function calculatePay()
    {
        $this->total_hours_worked = $this->calculateWorkedHours();

        // Calculate expected regular hours based on employee schedule
        $expectedRegularHours = $this->calculateExpectedRegularHours();

        // Regular hours are the minimum of worked hours and expected hours
        $this->regular_hours = min($this->total_hours_worked, $expectedRegularHours);
        $this->overtime_hours = max(0, $this->total_hours_worked - $expectedRegularHours);

        // Calculate pay
        $this->gross_pay = ($this->regular_hours * $this->hourly_rate) +
                            ($this->overtime_hours * $this->overtime_rate);

        // NEW: Calculate deductions using the dynamic deduction system
        $this->calculateDeductions();

        $this->net_pay = $this->gross_pay - $this->deductions;

        $this->save();
    }

    /**
     * Calculate deductions using the dynamic deduction system
     * Integrates with EmployeeDeduction and MandatoryDeduction models
     */
    public function calculateDeductions()
    {
        // Use gross pay as base for percentage calculations
        $baseSalary = $this->gross_pay;
        $totalDeductions = 0;

        // Reset individual deduction fields to 0
        $this->sss_deduction = 0;
        $this->philhealth_deduction = 0;
        $this->pagibig_deduction = 0;
        $this->tax_deduction = 0;
        $this->other_deductions = 0;

        // Get all enabled employee deductions with their mandatory deduction details
        $employeeDeductions = EmployeeDeduction::where('employee_id', $this->employee_id)
            ->enabled()
            ->with('deduction')
            ->get();

        foreach ($employeeDeductions as $employeeDeduction) {
            $deductionAmount = $employeeDeduction->calculateDeduction($baseSalary);
            $totalDeductions += $deductionAmount;

            // Store individual deduction amounts for reporting and backward compatibility
            $deductionType = $employeeDeduction->deduction->deduction_type;
            $this->{"{$deductionType}_deduction"} = $deductionAmount;
        }

        $this->deductions = $totalDeductions;
        return $totalDeductions;
    }

    /**
     * Get deduction summary for display purposes
     */
    public function getDeductionSummary()
    {
        $summary = [];

        $employeeDeductions = EmployeeDeduction::where('employee_id', $this->employee_id)
            ->enabled()
            ->with('deduction')
            ->get();

        foreach ($employeeDeductions as $employeeDeduction) {
            $deductionType = $employeeDeduction->deduction->deduction_type;
            $amount = $this->{"{$deductionType}_deduction"};

            if ($amount > 0) {
                $summary[] = [
                    'name' => $employeeDeduction->deduction->name,
                    'type' => $deductionType,
                    'amount' => $amount,
                    'description' => $employeeDeduction->deduction->description
                ];
            }
        }

        return $summary;
    }

    /**
     * Calculate expected regular hours based on employee schedule for the pay period
     */
    private function calculateExpectedRegularHours()
    {
        $employee = $this->employee;
        if (!$employee) {
            return 40; // Fallback to 40 hours if no employee found
        }

        $schedules = $employee->schedules;
        if ($schedules->isEmpty()) {
            return 40; // Fallback if no schedule found
        }

        $startDate = \Carbon\Carbon::parse($this->pay_period_start);
        $endDate = \Carbon\Carbon::parse($this->pay_period_end);

        $totalExpectedHours = 0;

        // Loop through each day in the pay period
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeekIso; // 1 = Monday, 7 = Sunday

            // Find schedule for this day
            $schedule = $schedules->where('day_of_week', $dayOfWeek)->first();

            if ($schedule && $schedule->start_time && $schedule->end_time) {
                $start = \Carbon\Carbon::parse($schedule->start_time);
                $end = \Carbon\Carbon::parse($schedule->end_time);

                // Calculate hours for this day
                $minutes = $start->diffInMinutes($end);
                $hours = $minutes / 60;
                $totalExpectedHours += $hours;
            }
        }

        return $totalExpectedHours > 0 ? $totalExpectedHours : 40; // Fallback to 40 if calculation fails
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);
    }

    /**
     * Recalculate this payroll record using current deduction settings
     */
    public function recalculate()
    {
        $this->calculatePay();
        return $this;
    }

    /**
     * Get the effective hourly rate for this employee
     */
    public function getEffectiveHourlyRate()
    {
        if ($this->employee && $this->employee->employeeProfile) {
            return $this->employee->employeeProfile->hourly_rate;
        }
        return $this->hourly_rate;
    }

    /**
     * Get the effective overtime rate for this employee
     */
    public function getEffectiveOvertimeRate()
    {
        // Default to 1.5x hourly rate if not explicitly set
        return $this->overtime_rate ?: ($this->getEffectiveHourlyRate() * 1.5);
    }
}
