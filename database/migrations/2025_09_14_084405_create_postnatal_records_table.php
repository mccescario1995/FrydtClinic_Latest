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
        Schema::create('postnatal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->integer('visit_number')->default(1);
            $table->date('visit_date');
            $table->integer('days_postpartum')->nullable();
            $table->integer('weeks_postpartum')->nullable();

            // Vital Signs
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('oxygen_saturation')->nullable();

            // Postnatal Assessment
            $table->string('general_condition', 100)->nullable();
            $table->string('breast_condition', 100)->nullable();
            $table->string('uterus_condition', 100)->nullable();
            $table->string('perineum_condition', 100)->nullable();
            $table->string('lochia_condition', 100)->nullable();
            $table->string('episiotomy_condition', 100)->nullable();

            // Breastfeeding
            $table->string('breastfeeding_status', 100)->nullable();
            $table->text('breastfeeding_notes')->nullable();
            $table->string('latch_assessment', 100)->nullable();

            // Newborn Care (if applicable)
            $table->boolean('newborn_check')->default(false);
            $table->decimal('newborn_weight', 5, 2)->nullable();
            $table->text('newborn_notes')->nullable();

            // Family Planning
            $table->string('family_planning_method', 100)->nullable();
            $table->text('family_planning_counseling')->nullable();

            // Chief Complaint & Assessment
            $table->text('chief_complaint')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();

            // Medications & Instructions
            $table->json('medications_prescribed')->nullable();
            $table->text('instructions_given')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('next_visit_type', 100)->nullable();

            // Notes & Alerts
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('postnatal_records');
    }
};
