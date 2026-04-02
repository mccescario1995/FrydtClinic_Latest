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
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('deduction_id')->constrained('mandatory_deductions')->onDelete('cascade');
            $table->decimal('custom_percentage_rate', 10, 4)->nullable(); // Override percentage rate for this employee
            $table->decimal('custom_fixed_amount', 10, 2)->nullable(); // Override fixed amount for this employee
            $table->boolean('is_enabled')->default(true); // Whether this deduction is enabled for this employee
            $table->text('notes')->nullable(); // Additional notes for this employee's deduction
            $table->timestamps();

            $table->unique(['employee_id', 'deduction_id']); // One deduction config per employee per deduction type
            $table->index('employee_id');
            $table->index('deduction_id');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
    }
};
