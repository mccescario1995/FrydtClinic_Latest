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
        Schema::create('postpartum_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->integer('visit_number')->default(1);
            $table->date('visit_date');
            $table->integer('weeks_postpartum')->nullable();
            $table->integer('days_postpartum')->nullable();

            // Vital Signs
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();

            // Physical Assessment
            $table->string('general_condition', 100)->nullable();
            $table->string('breast_condition', 100)->nullable();
            $table->string('uterus_condition', 100)->nullable();
            $table->string('perineum_condition', 100)->nullable();
            $table->string('lochia_condition', 100)->nullable();
            $table->string('episiotomy_condition', 100)->nullable();

            // Mental Health Assessment
            $table->string('mood_assessment', 100)->nullable();
            $table->text('emotional_support_needs')->nullable();
            $table->boolean('postpartum_depression_screening')->nullable();
            $table->text('mental_health_notes')->nullable();

            // Breastfeeding & Infant Care
            $table->string('breastfeeding_status', 100)->nullable();
            $table->text('breastfeeding_challenges')->nullable();
            $table->text('lactation_support')->nullable();
            $table->boolean('infant_feeding_assessment')->nullable();
            $table->text('infant_care_education')->nullable();

            // Contraception & Family Planning
            $table->string('contraceptive_method', 100)->nullable();
            $table->text('family_planning_counseling')->nullable();
            $table->date('next_contraceptive_visit')->nullable();

            // Complications & Follow-up
            $table->text('postpartum_complications')->nullable();
            $table->text('medications_prescribed')->nullable();
            $table->text('wound_care_instructions')->nullable();
            $table->text('activity_restrictions')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('follow_up_reason', 200)->nullable();

            // Education & Counseling
            $table->json('education_provided')->nullable();
            $table->text('nutrition_counseling')->nullable();
            $table->text('exercise_guidance')->nullable();
            $table->text('warning_signs_education')->nullable();

            // Notes & Alerts
            $table->text('assessment_notes')->nullable();
            $table->text('plan_notes')->nullable();
            $table->json('alerts_flags')->nullable();
            $table->json('quality_indicators_met')->nullable();

            $table->timestamps();

            $table->index(['patient_id', 'visit_date']);
            $table->index('provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postpartum_records');
    }
};
