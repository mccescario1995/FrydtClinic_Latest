<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration sets up default employee deductions for existing employees
        // Run this after ensuring mandatory deductions exist

        // Get all mandatory deductions that should be enabled by default
        $defaultDeductions = \App\Models\MandatoryDeduction::where('is_active', true)->get();

        if ($defaultDeductions->isEmpty()) {
            // Create default Philippine deductions if none exist
            $this->createDefaultPhilippineDeductions();
            $defaultDeductions = \App\Models\MandatoryDeduction::where('is_active', true)->get();
        }

        // Get all employees who don't have any deductions configured
        $employees = \App\Models\User::where('user_type', 'employee')->get();

        foreach ($employees as $employee) {
            // Check if employee already has deductions
            $existingDeductions = \App\Models\EmployeeDeduction::where('employee_id', $employee->id)->count();

            if ($existingDeductions == 0) {
                // Create default deductions for this employee
                foreach ($defaultDeductions as $deduction) {
                    \App\Models\EmployeeDeduction::create([
                        'employee_id' => $employee->id,
                        'deduction_id' => $deduction->id,
                        'is_enabled' => true, // Enable by default
                        'custom_percentage_rate' => null, // Use default rate
                        'custom_fixed_amount' => null, // Use default amount
                        'notes' => 'Auto-configured default deduction',
                    ]);
                }

                echo "✓ Configured default deductions for employee: {$employee->name}\n";
            }
        }

        echo "✓ Default employee deductions setup completed!\n";
    }

    /**
     * Create default Philippine mandatory deductions
     */
    private function createDefaultPhilippineDeductions()
    {
        $deductions = [
            [
                'deduction_type' => 'sss',
                'name' => 'SSS (Social Security System)',
                'description' => 'Philippine Social Security System contribution',
                'percentage_rate' => 4.5, // Employee share (example rate)
                'fixed_amount' => 0,
                'minimum_base_salary' => 1000,
                'maximum_deduction' => 800,
                'is_active' => true,
                'effective_date' => now(),
                'notes' => 'Default SSS deduction for Philippine compliance',
            ],
            [
                'deduction_type' => 'philhealth',
                'name' => 'PhilHealth',
                'description' => 'Philippine Health Insurance Corporation contribution',
                'percentage_rate' => 2.75, // Employee share (example rate)
                'fixed_amount' => 0,
                'minimum_base_salary' => 1000,
                'maximum_deduction' => 600,
                'is_active' => true,
                'effective_date' => now(),
                'notes' => 'Default PhilHealth deduction for Philippine compliance',
            ],
            [
                'deduction_type' => 'pagibig',
                'name' => 'Pag-IBIG (HDMF)',
                'description' => 'Home Development Mutual Fund contribution',
                'percentage_rate' => 2.0, // Employee share (example rate)
                'fixed_amount' => 0,
                'minimum_base_salary' => 1000,
                'maximum_deduction' => 100,
                'is_active' => true,
                'effective_date' => now(),
                'notes' => 'Default Pag-IBIG deduction for Philippine compliance',
            ],
            [
                'deduction_type' => 'tax',
                'name' => 'Withholding Tax',
                'description' => 'Income tax withholding based on salary bracket',
                'percentage_rate' => 0, // Variable rate based on tax table
                'fixed_amount' => 0,
                'minimum_base_salary' => 2083, // Tax exemption threshold
                'maximum_deduction' => 0,
                'is_active' => true,
                'effective_date' => now(),
                'notes' => 'Withholding tax - rate varies by salary bracket',
            ],
        ];

        foreach ($deductions as $deductionData) {
            // Check if deduction type already exists
            $existing = \App\Models\MandatoryDeduction::where('deduction_type', $deductionData['deduction_type'])->first();

            if (!$existing) {
                \App\Models\MandatoryDeduction::create($deductionData);
                echo "✓ Created default deduction: {$deductionData['name']}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This would remove all employee deductions (use with caution)
        // Uncomment if you want to allow rollback

        /*
        \App\Models\EmployeeDeduction::where('notes', 'Auto-configured default deduction')->delete();
        echo "✓ Removed auto-configured employee deductions\n";
        */
    }
};
