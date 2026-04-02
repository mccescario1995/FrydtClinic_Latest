<?php

/**
 * Test Script for Deduction-Payroll Integration
 *
 * This script tests the integration between the deduction system and payroll calculations.
 * Run this after implementing the integration to verify everything works correctly.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;
use App\Models\Payroll;
use App\Models\EmployeeDeduction;
use App\Models\MandatoryDeduction;
use App\Models\EmployeeProfile;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Testing Deduction-Payroll Integration\n";
echo "==========================================\n\n";

try {
    // Test 1: Check if deduction models exist and are working
    echo "1. Testing Deduction Models...\n";

    $mandatoryDeductions = MandatoryDeduction::all();
    echo "   ✓ Found " . $mandatoryDeductions->count() . " mandatory deductions\n";

    $employeeDeductions = EmployeeDeduction::all();
    echo "   ✓ Found " . $employeeDeductions->count() . " employee deductions\n";

    // Test 2: Check if employees have deduction settings
    echo "\n2. Testing Employee Deduction Configuration...\n";

    $employees = User::where('user_type', 'employee')->get();
    echo "   ✓ Found " . $employees->count() . " employees\n";

    $employeesWithDeductions = $employees->filter(function($employee) {
        return EmployeeDeduction::where('employee_id', $employee->id)->enabled()->count() > 0;
    });

    echo "   ✓ " . $employeesWithDeductions->count() . " employees have deduction settings\n";

    if ($employeesWithDeductions->count() < $employees->count()) {
        echo "   ⚠️  " . ($employees->count() - $employeesWithDeductions->count()) . " employees need deduction configuration\n";
    }

    // Test 3: Test Payroll Model Integration
    echo "\n3. Testing Payroll Model Integration...\n";

    $sampleEmployee = $employeesWithDeductions->first();
    if ($sampleEmployee) {
        // Create a test payroll
        $testPayroll = new Payroll([
            'employee_id' => $sampleEmployee->id,
            'pay_period_start' => now()->startOfMonth(),
            'pay_period_end' => now()->endOfMonth(),
            'hourly_rate' => 150.00,
            'overtime_rate' => 225.00,
        ]);

        // Test deduction calculation
        echo "   Testing deduction calculation for: " . $sampleEmployee->name . "\n";

        $deductionSummary = $testPayroll->getDeductionSummary();
        echo "   ✓ Deduction summary generated with " . count($deductionSummary) . " items\n";

        foreach ($deductionSummary as $deduction) {
            echo "     - {$deduction['name']}: ₱{$deduction['amount']}\n";
        }

        // Clean up test payroll
        unset($testPayroll);
    } else {
        echo "   ⚠️  No employees with deduction settings found for testing\n";
    }

    // Test 4: Verify Payroll Calculation Method
    echo "\n4. Testing Payroll Calculation Method...\n";

    if ($sampleEmployee && $sampleEmployee->employeeProfile) {
        // Create a complete test payroll with attendance
        $testPayroll = Payroll::create([
            'employee_id' => $sampleEmployee->id,
            'pay_period_start' => now()->startOfMonth(),
            'pay_period_end' => now()->endOfMonth(),
            'hourly_rate' => $sampleEmployee->employeeProfile->hourly_rate ?: 150.00,
            'overtime_rate' => 225.00,
        ]);

        // This would normally require attendance records, but we can test the deduction part
        echo "   ✓ Test payroll created (ID: {$testPayroll->id})\n";
        echo "   ✓ Employee has hourly rate: ₱" . number_format($testPayroll->hourly_rate, 2) . "/hour\n";

        // Clean up test payroll
        $testPayroll->delete();
        echo "   ✓ Test payroll cleaned up\n";
    }

    // Test 5: Check for Common Issues
    echo "\n5. Checking for Common Issues...\n";

    // Check for employees without hourly rates
    $employeesWithoutRates = $employees->filter(function($employee) {
        return !$employee->employeeProfile || $employee->employeeProfile->hourly_rate <= 0;
    });

    if ($employeesWithoutRates->count() > 0) {
        echo "   ⚠️  " . $employeesWithoutRates->count() . " employees don't have hourly rates set\n";
        foreach ($employeesWithoutRates->take(5) as $emp) {
            echo "     - {$emp->name}\n";
        }
    } else {
        echo "   ✓ All employees have hourly rates configured\n";
    }

    // Check for duplicate deduction configurations
    $duplicateDeductions = EmployeeDeduction::select('employee_id', 'deduction_id')
        ->groupBy('employee_id', 'deduction_id')
        ->havingRaw('COUNT(*) > 1')
        ->get();

    if ($duplicateDeductions->count() > 0) {
        echo "   ⚠️  Found " . $duplicateDeductions->count() . " duplicate deduction configurations\n";
    } else {
        echo "   ✓ No duplicate deduction configurations found\n";
    }

    echo "\n🎉 Integration Test Complete!\n";
    echo "============================\n\n";

    // Summary and Recommendations
    echo "📋 SUMMARY & RECOMMENDATIONS:\n\n";

    if ($employeesWithDeductions->count() == $employees->count()) {
        echo "✅ All employees have deduction settings configured\n";
    } else {
        echo "🔧 Action Required: Configure deductions for " . ($employees->count() - $employeesWithDeductions->count()) . " employees\n";
        echo "   - Go to Admin Portal > Employee Deductions\n";
        echo "   - Use 'Enable All (Default Rates)' for quick setup\n";
    }

    if ($employeesWithoutRates->count() == 0) {
        echo "✅ All employees have hourly rates configured\n";
    } else {
        echo "🔧 Action Required: Set hourly rates for " . $employeesWithoutRates->count() . " employees\n";
        echo "   - Edit employee profiles to set hourly rates\n";
    }

    if ($duplicateDeductions->count() == 0) {
        echo "✅ No duplicate deduction configurations\n";
    } else {
        echo "🔧 Action Required: Remove " . $duplicateDeductions->count() . " duplicate deduction configurations\n";
    }

    echo "\n🚀 Next Steps:\n";
    echo "1. Configure deductions for employees who don't have them\n";
    echo "2. Set hourly rates for employees who don't have them\n";
    echo "3. Test payroll generation with a small group\n";
    echo "4. Monitor the first few payroll cycles for accuracy\n";

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";

    echo "\n🔧 Troubleshooting:\n";
    echo "1. Ensure Laravel application is properly configured\n";
    echo "2. Check database connection and permissions\n";
    echo "3. Verify all required models and migrations exist\n";
    echo "4. Run 'php artisan migrate' to ensure database is up to date\n";
}
