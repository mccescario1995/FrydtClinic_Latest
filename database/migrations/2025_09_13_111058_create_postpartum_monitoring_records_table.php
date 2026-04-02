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
        Schema::create('postpartum_monitoring_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('labor_delivery_record_id')->nullable()->constrained('labor_delivery_records')->onDelete('set null');
            $table->foreignId('attending_physician_id')->nullable()->constrained('users');
            $table->foreignId('midwife_id')->nullable()->constrained('users');

            // Visit Information
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->integer('days_postpartum'); // Days since delivery
            $table->integer('weeks_postpartum'); // Weeks since delivery

            // Maternal Vital Signs
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->decimal('temperature_celsius', 4, 2)->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();

            // Physical Examination
            $table->enum('general_condition', ['good', 'fair', 'poor'])->nullable();
            $table->text('breast_examination')->nullable();
            $table->text('uterus_examination')->nullable();
            $table->text('perineum_examination')->nullable();
            $table->text('lochia_assessment')->nullable();
            $table->text('extremities_examination')->nullable();

            // Uterine Involution
            $table->decimal('fundal_height_cm', 4, 2)->nullable();
            $table->string('uterine_tone')->nullable();
            $table->string('lochia_color')->nullable();
            $table->string('lochia_amount')->nullable();
            $table->string('lochia_odor')->nullable();

            // Breastfeeding Assessment
            $table->enum('breastfeeding_status', ['exclusive', 'mixed', 'formula', 'none'])->nullable();
            $table->text('breastfeeding_issues')->nullable();
            $table->text('lactation_support')->nullable();

            // Mental Health Assessment
            $table->enum('mood_assessment', ['normal', 'mild_depression', 'severe_depression'])->nullable();
            $table->text('emotional_support_needs')->nullable();
            $table->boolean('postpartum_depression_screening')->default(false);

            // Contraceptive Counseling
            $table->text('contraceptive_method_discussed')->nullable();
            $table->text('family_planning_counseling')->nullable();

            // Immunization Status
            $table->boolean('td_vaccine_administered')->default(false);
            $table->date('td_vaccine_date')->nullable();

            // Laboratory Tests
            $table->string('hemoglobin_level')->nullable();
            $table->string('hematocrit_level')->nullable();
            $table->string('urinalysis')->nullable();
            $table->text('other_laboratory_results')->nullable();

            // Medications and Supplements
            $table->text('current_medications')->nullable();
            $table->boolean('iron_supplements')->default(false);
            $table->boolean('calcium_supplements')->default(false);
            $table->boolean('vitamin_supplements')->default(false);

            // Complications and Concerns
            $table->text('postpartum_complications')->nullable();
            $table->text('patient_concerns')->nullable();
            $table->text('referrals_needed')->nullable();

            // Newborn Status (if applicable)
            $table->text('newborn_health_status')->nullable();
            $table->text('infant_feeding_status')->nullable();
            $table->text('newborn_care_instructions')->nullable();

            // Follow-up and Counseling
            $table->date('next_visit_date')->nullable();
            $table->text('follow_up_instructions')->nullable();
            $table->text('health_education_topics')->nullable();

            // Documentation
            $table->text('physician_notes')->nullable();
            $table->text('midwife_notes')->nullable();
            $table->text('nurse_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'visit_date']);
            $table->index('labor_delivery_record_id');
            $table->index('days_postpartum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postpartum_monitoring_records');
    }
};
