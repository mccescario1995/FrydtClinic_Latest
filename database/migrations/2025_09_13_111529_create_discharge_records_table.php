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
        Schema::create('discharge_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('discharged_by')->constrained('users'); // Doctor or midwife
            $table->foreignId('labor_delivery_record_id')->nullable()->constrained('labor_delivery_records')->onDelete('set null');

            // Discharge Information
            $table->dateTime('discharge_date_time');
            $table->dateTime('admission_date_time')->nullable();
            $table->integer('length_of_stay_days')->nullable();

            // Discharge Type and Reason
            $table->enum('discharge_type', [
                'routine_discharge',
                'against_medical_advice',
                'transfer_to_hospital',
                'transfer_to_another_facility',
                'death',
                'maternity_discharge'
            ]);
            $table->text('discharge_reason')->nullable();

            // Final Diagnosis
            $table->text('primary_diagnosis')->nullable();
            $table->text('secondary_diagnoses')->nullable();
            $table->text('complications_during_stay')->nullable();

            // Treatment Summary
            $table->text('treatments_received')->nullable();
            $table->text('medications_at_discharge')->nullable();
            $table->text('procedures_performed')->nullable();

            // Vital Signs at Discharge
            $table->decimal('discharge_blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('discharge_blood_pressure_diastolic', 5, 2)->nullable();
            $table->integer('discharge_pulse_rate')->nullable();
            $table->decimal('discharge_temperature_celsius', 4, 2)->nullable();
            $table->decimal('discharge_weight_kg', 5, 2)->nullable();

            // Condition at Discharge
            $table->enum('condition_at_discharge', [
                'stable',
                'improved',
                'unchanged',
                'worsened',
                'critical'
            ])->nullable();
            $table->text('condition_notes')->nullable();

            // Discharge Instructions
            $table->text('activity_restrictions')->nullable();
            $table->text('dietary_instructions')->nullable();
            $table->text('medication_instructions')->nullable();
            $table->text('wound_care_instructions')->nullable();
            $table->text('symptoms_to_watch_for')->nullable();

            // Follow-up Care
            $table->date('follow_up_date')->nullable();
            $table->string('follow_up_facility')->nullable();
            $table->text('follow_up_instructions')->nullable();
            $table->text('referrals_needed')->nullable();

            // Family Planning and Counseling
            $table->text('family_planning_counseling')->nullable();
            $table->text('contraceptive_method_recommended')->nullable();
            $table->text('postpartum_care_instructions')->nullable();

            // Newborn Care (if applicable)
            $table->text('newborn_care_instructions')->nullable();
            $table->text('breastfeeding_support')->nullable();
            $table->text('infant_feeding_instructions')->nullable();

            // Emergency Contact Information
            $table->text('emergency_contact_instructions')->nullable();
            $table->text('when_to_seek_help')->nullable();

            // Transportation and Support
            $table->text('transportation_arrangements')->nullable();
            $table->text('home_support_assessment')->nullable();

            // Documentation
            $table->text('discharge_summary')->nullable();
            $table->text('physician_notes')->nullable();
            $table->text('nursing_notes')->nullable();

            // Billing and Insurance
            $table->decimal('total_charges', 10, 2)->nullable();
            $table->decimal('philhealth_coverage', 10, 2)->nullable();
            $table->decimal('patient_responsibility', 10, 2)->nullable();
            $table->text('billing_notes')->nullable();

            // Quality Assurance
            $table->boolean('patient_satisfaction_survey_completed')->default(false);
            $table->text('quality_improvement_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'discharge_date_time']);
            $table->index('discharged_by');
            $table->index('discharge_type');
            $table->index('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discharge_records');
    }
};
