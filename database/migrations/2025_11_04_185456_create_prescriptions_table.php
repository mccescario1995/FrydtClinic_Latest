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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->foreignId('prescribed_by')->constrained('users');
            $table->foreignId('dispensed_by')->nullable()->constrained('users');

            // Prescription Details
            $table->string('prescription_number')->unique();
            $table->integer('quantity_prescribed');
            $table->integer('quantity_dispensed')->default(0);
            $table->string('dosage_instructions');
            $table->integer('duration_days')->nullable();
            $table->text('special_instructions')->nullable();

            // Dispensing Information
            $table->dateTime('prescribed_date');
            $table->dateTime('dispensed_date')->nullable();
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->decimal('total_price', 8, 2)->nullable();
            $table->boolean('covered_by_philhealth')->default(false);

            // Status
            $table->enum('status', [
                'prescribed',
                'partially_dispensed',
                'fully_dispensed',
                'cancelled'
            ])->default('prescribed');

            // Notes
            $table->text('pharmacist_notes')->nullable();
            $table->text('physician_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['treatment_id', 'inventory_id']);
            $table->index('prescription_number');
            $table->index('status');
            $table->index('prescribed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
