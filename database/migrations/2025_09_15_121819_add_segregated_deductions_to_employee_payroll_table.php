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
        Schema::table('employee_payroll', function (Blueprint $table) {
            $table->decimal('sss_deduction', 8, 2)->default(0)->after('deductions');
            $table->decimal('philhealth_deduction', 8, 2)->default(0)->after('sss_deduction');
            $table->decimal('pagibig_deduction', 8, 2)->default(0)->after('philhealth_deduction');
            $table->decimal('tax_deduction', 8, 2)->default(0)->after('pagibig_deduction');
            $table->decimal('other_deductions', 8, 2)->default(0)->after('tax_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_payroll', function (Blueprint $table) {
            $table->dropColumn([
                'sss_deduction',
                'philhealth_deduction',
                'pagibig_deduction',
                'tax_deduction',
                'other_deductions'
            ]);
        });
    }
};
