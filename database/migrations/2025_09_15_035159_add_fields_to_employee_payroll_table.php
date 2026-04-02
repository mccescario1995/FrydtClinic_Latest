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
            $table->unsignedBigInteger('employee_id');
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->decimal('total_hours_worked', 8, 2)->default(0);
            $table->decimal('regular_hours', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('hourly_rate', 8, 2)->default(0);
            $table->decimal('overtime_rate', 8, 2)->default(0);
            $table->decimal('gross_pay', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processed', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_payroll', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn([
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
                'net_pay',
                'status',
                'payment_date',
                'notes',
            ]);
        });
    }
};
