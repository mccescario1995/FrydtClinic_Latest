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
        Schema::create('mandatory_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('deduction_type')->unique(); // sss, philhealth, pagibig, tax, other
            $table->string('name'); // Display name
            $table->text('description')->nullable(); // Description of the deduction
            $table->decimal('percentage_rate', 10, 4)->default(0); // Percentage rate (e.g., 12.5 for 12.5%)
            $table->decimal('fixed_amount', 10, 2)->default(0); // Fixed amount deduction
            $table->decimal('minimum_base_salary', 10, 2)->default(0); // Minimum salary to apply this deduction
            $table->decimal('maximum_deduction', 10, 2)->nullable(); // Maximum deduction amount (nullable = no limit)
            $table->boolean('is_active')->default(true); // Whether this deduction is currently active
            $table->date('effective_date')->nullable(); // When this deduction rate became effective
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();

            $table->index('deduction_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandatory_deductions');
    }
};
