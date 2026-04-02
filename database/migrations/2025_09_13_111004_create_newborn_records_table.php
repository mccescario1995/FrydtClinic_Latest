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
        Schema::create('newborn_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('labor_delivery_record_id')->nullable()->constrained('labor_delivery_records')->onDelete('set null');
            $table->foreignId('attending_pediatrician_id')->nullable()->constrained('users');

            // Newborn Identification
            $table->string('newborn_name')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->integer('birth_order')->default(1); // 1st, 2nd, 3rd infant, etc.

            // Birth Information
            $table->dateTime('date_time_of_birth');
            $table->integer('gestational_age_weeks')->nullable();
            $table->integer('gestational_age_days')->nullable();
            $table->enum('birth_type', ['singleton', 'twin', 'triplet', 'other'])->default('singleton');

            // Physical Measurements
            $table->decimal('birth_weight_grams', 6, 2)->nullable();
            $table->decimal('birth_length_cm', 5, 2)->nullable();
            $table->decimal('head_circumference_cm', 5, 2)->nullable();
            $table->decimal('chest_circumference_cm', 5, 2)->nullable();
            $table->decimal('abdominal_circumference_cm', 5, 2)->nullable();

            // APGAR Scores
            $table->integer('apgar_score_1min')->nullable();
            $table->integer('apgar_score_5min')->nullable();
            $table->integer('apgar_score_10min')->nullable();

            // Initial Assessment
            $table->string('skin_color')->nullable();
            $table->string('respiratory_effort')->nullable();
            $table->string('heart_rate')->nullable();
            $table->string('muscle_tone')->nullable();
            $table->string('reflex_irritability')->nullable();

            // Vital Signs
            $table->integer('temperature_celsius')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable();

            // Physical Examination
            $table->text('general_appearance')->nullable();
            $table->text('head_examination')->nullable();
            $table->text('eyes_examination')->nullable();
            $table->text('ears_examination')->nullable();
            $table->text('nose_examination')->nullable();
            $table->text('mouth_examination')->nullable();
            $table->text('neck_examination')->nullable();
            $table->text('chest_examination')->nullable();
            $table->text('heart_examination')->nullable();
            $table->text('lungs_examination')->nullable();
            $table->text('abdomen_examination')->nullable();
            $table->text('genitalia_examination')->nullable();
            $table->text('anus_examination')->nullable();
            $table->text('extremities_examination')->nullable();
            $table->text('back_examination')->nullable();
            $table->text('skin_examination')->nullable();

            // Congenital Anomalies
            $table->text('congenital_anomalies')->nullable();
            $table->text('birth_defects')->nullable();

            // Immediate Care
            $table->boolean('received_vitamin_k')->default(false);
            $table->boolean('received_hep_b_vaccine')->default(false);
            $table->boolean('received_eye_prophylaxis')->default(false);
            $table->text('immediate_care_notes')->nullable();

            // Feeding
            $table->enum('feeding_method', ['breastfeeding', 'formula', 'mixed'])->nullable();
            $table->time('first_breastfeeding')->nullable();
            $table->text('feeding_notes')->nullable();

            // Laboratory Tests
            $table->string('cord_blood_type')->nullable();
            $table->text('newborn_screening_results')->nullable();
            $table->text('other_laboratory_tests')->nullable();

            // Discharge Information
            $table->dateTime('discharge_date_time')->nullable();
            $table->decimal('discharge_weight_grams', 6, 2)->nullable();
            $table->text('discharge_instructions')->nullable();
            $table->text('follow_up_instructions')->nullable();

            // Complications and Interventions
            $table->text('complications')->nullable();
            $table->text('medical_interventions')->nullable();
            $table->text('treatments_received')->nullable();

            // Status
            $table->enum('newborn_status', ['healthy', 'needs_observation', 'critical', 'deceased'])->default('healthy');
            $table->text('status_notes')->nullable();

            // Documentation
            $table->text('pediatrician_notes')->nullable();
            $table->text('nurse_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'date_time_of_birth']);
            $table->index('labor_delivery_record_id');
            $table->index('newborn_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newborn_records');
    }
};
