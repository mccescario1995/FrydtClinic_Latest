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
        Schema::create('prenatal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            // Pregnancy Information
            $table->date('last_menstrual_period')->nullable();
            $table->date('estimated_due_date')->nullable();
            $table->integer('gestational_age_weeks')->nullable();
            $table->integer('gestational_age_days')->nullable();
            $table->integer('gravida')->default(1); // Number of pregnancies
            $table->integer('para')->default(0); // Number of births
            $table->integer('abortion')->default(0); // Number of abortions
            $table->integer('living_children')->default(0);

            // Prenatal Visit Information
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->foreignId('attending_physician_id')->nullable()->constrained('users');
            $table->foreignId('midwife_id')->nullable()->constrained('users');

            // Vital Signs
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('bmi', 4, 2)->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('temperature_celsius', 4, 2)->nullable();

            // Fetal Assessment
            $table->integer('fetal_heart_rate')->nullable();
            $table->string('fetal_position')->nullable();
            $table->string('fetal_presentation')->nullable();
            $table->decimal('fundal_height_cm', 4, 2)->nullable();

            // Laboratory Results
            $table->string('blood_type')->nullable();
            $table->string('hemoglobin_level')->nullable();
            $table->string('hematocrit_level')->nullable();
            $table->string('urinalysis')->nullable();
            $table->string('vdrl_test')->nullable();
            $table->string('hbsag_test')->nullable();

            // Risk Assessment
            $table->text('risk_factors')->nullable();
            $table->text('complications')->nullable();
            $table->enum('risk_level', ['low', 'moderate', 'high'])->default('low');

            // Immunization
            $table->boolean('td_vaccine_given')->default(false);
            $table->date('td_vaccine_date')->nullable();
            $table->integer('td_vaccine_dose')->nullable();

            // Medications and Supplements
            $table->text('medications')->nullable();
            $table->boolean('iron_supplements')->default(false);
            $table->boolean('calcium_supplements')->default(false);
            $table->boolean('vitamin_supplements')->default(false);

            // Counseling and Education
            $table->text('counseling_topics')->nullable();
            $table->text('patient_education')->nullable();

            // Next Visit
            $table->date('next_visit_date')->nullable();
            $table->text('next_visit_notes')->nullable();

            // Status and Notes
            $table->enum('pregnancy_status', ['active', 'completed', 'terminated'])->default('active');
            $table->text('general_notes')->nullable();
            $table->text('physician_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'visit_date']);
            $table->index('estimated_due_date');
            $table->index('pregnancy_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prenatal_records');
    }
};
