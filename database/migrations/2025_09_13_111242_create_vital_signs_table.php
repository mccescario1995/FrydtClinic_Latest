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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users'); // Nurse, doctor, or midwife

            // Recording Information
            $table->dateTime('recorded_at');
            $table->string('visit_type')->nullable(); // prenatal, postnatal, general, etc.

            // Basic Vital Signs
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable();
            $table->integer('pulse_rate')->nullable(); // beats per minute
            $table->integer('respiratory_rate')->nullable(); // breaths per minute
            $table->decimal('temperature_celsius', 4, 2)->nullable();
            $table->decimal('temperature_fahrenheit', 4, 2)->nullable();

            // Weight and Measurements
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('bmi', 4, 2)->nullable();
            $table->decimal('waist_circumference_cm', 5, 2)->nullable();

            // Pregnancy-Specific Measurements
            $table->decimal('fundal_height_cm', 4, 2)->nullable();
            $table->integer('fetal_heart_rate')->nullable();
            $table->string('fetal_position')->nullable();

            // Pain Assessment
            $table->integer('pain_scale')->nullable(); // 0-10 scale
            $table->string('pain_location')->nullable();
            $table->string('pain_character')->nullable();

            // Oxygen Saturation
            $table->decimal('oxygen_saturation', 4, 2)->nullable(); // SpO2 percentage
            $table->string('oxygen_method')->nullable(); // room air, supplemental oxygen, etc.

            // Neurological Assessment
            $table->string('level_of_consciousness')->nullable(); // alert, drowsy, etc.
            $table->string('pupil_response')->nullable();
            $table->integer('glasgow_coma_scale')->nullable();

            // Cardiovascular Assessment
            $table->string('heart_rhythm')->nullable();
            $table->string('capillary_refill_time')->nullable();
            $table->string('edema')->nullable();

            // Respiratory Assessment
            $table->string('breathing_pattern')->nullable();
            $table->string('lung_sounds')->nullable();
            $table->string('cough')->nullable();

            // Additional Measurements
            $table->decimal('blood_glucose_mg_dl', 6, 2)->nullable();
            $table->integer('urine_output_ml')->nullable();
            $table->string('stool_character')->nullable();

            // Assessment Notes
            $table->text('general_assessment')->nullable();
            $table->text('abnormal_findings')->nullable();
            $table->text('nursing_notes')->nullable();

            // Status and Alerts
            $table->enum('urgency_level', ['normal', 'elevated', 'urgent', 'critical'])->default('normal');
            $table->boolean('requires_attention')->default(false);
            $table->text('alert_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'recorded_at']);
            $table->index('recorded_by');
            $table->index('urgency_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
