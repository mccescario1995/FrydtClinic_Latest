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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Service Information
            $table->string('name');
            $table->string('code')->unique(); // Service code for billing
            $table->text('description')->nullable();
            $table->enum('type', [
                'single',
                'package'
            ])->default('single');
            $table->enum('service_type', [
                'consultation',
                'procedure',
                'laboratory',
                'imaging',
                'therapy',
                'vaccination',
                'prenatal_care',
                'delivery',
                'postnatal_care',
                'other'
            ])->default('consultation');

            $table->enum('category', [
                'general_practice',
                'obstetrics_gynecology',
                'pediatrics',
                'internal_medicine',
                'surgery',
                'emergency_care',
                'preventive_care',
                'diagnostic',
                'therapeutic'
            ])->default('general_practice');

            // Pricing Information
            $table->decimal('base_price', 8, 2)->default(0);
            $table->decimal('philhealth_price', 8, 2)->nullable();
            $table->boolean('philhealth_covered')->default(false);
            $table->decimal('discount_percentage', 5, 2)->default(0);

            // Service Details
            $table->integer('duration_minutes')->nullable();
            $table->text('preparation_instructions')->nullable();
            $table->text('post_service_instructions')->nullable();
            $table->text('contraindications')->nullable();

            // Resource Requirements
            $table->text('required_equipment')->nullable();
            $table->text('required_supplies')->nullable();
            $table->text('staff_requirements')->nullable();

            // Status and Availability
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('requires_appointment')->default(true);
            $table->boolean('available_emergency')->default(false);
            $table->boolean('requires_lab_results')->default(false);

            // Scheduling
            $table->integer('advance_booking_days')->nullable();
            $table->json('available_days')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Quality and Compliance
            $table->text('quality_indicators')->nullable();
            $table->text('regulatory_requirements')->nullable();
            $table->date('last_review_date')->nullable();
            $table->date('next_review_date')->nullable();

            // Documentation
            $table->text('consent_form_required')->nullable();
            $table->text('documentation_requirements')->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('service_type');
            $table->index('category');
            $table->index('status');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
