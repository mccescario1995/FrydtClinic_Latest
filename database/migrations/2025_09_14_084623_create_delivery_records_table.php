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
        Schema::create('delivery_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('attending_provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('delivering_provider_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('anesthesiologist_id')->nullable()->constrained('users')->onDelete('set null');

            // Delivery Information
            $table->dateTime('admission_date_time');
            $table->dateTime('labor_onset_date_time')->nullable();
            $table->dateTime('rupture_of_membranes_date_time')->nullable();
            $table->string('rupture_of_membranes_type', 50)->nullable(); // Spontaneous, Artificial
            $table->dateTime('delivery_date_time');
            $table->string('delivery_type', 50)->nullable(); // Vaginal, Cesarean, Forceps, Vacuum
            $table->string('delivery_place', 100)->nullable();

            // Prenatal History
            $table->integer('gravida')->nullable(); // Number of pregnancies
            $table->integer('para')->nullable(); // Number of deliveries
            $table->integer('living_children')->nullable();
            $table->text('prenatal_history')->nullable();
            $table->text('risk_factors')->nullable();

            // Labor Progress
            $table->integer('labor_duration_hours')->nullable();
            $table->integer('labor_duration_minutes')->nullable();
            $table->string('labor_progress', 200)->nullable();
            $table->text('labor_complications')->nullable();

            // Delivery Details
            $table->string('presentation', 50)->nullable(); // Cephalic, Breech, etc.
            $table->string('position', 50)->nullable();
            $table->boolean('episiotomy_performed')->default(false);
            $table->string('episiotomy_degree', 20)->nullable();
            $table->text('perineal_tear')->nullable();
            $table->text('delivery_complications')->nullable();

            // Anesthesia
            $table->string('anesthesia_type', 50)->nullable();
            $table->text('anesthesia_notes')->nullable();

            // Newborn Information
            $table->string('newborn_gender', 10)->nullable();
            $table->decimal('newborn_weight', 5, 2)->nullable();
            $table->decimal('newborn_length', 4, 1)->nullable();
            $table->integer('newborn_apgar_1min')->nullable();
            $table->integer('newborn_apgar_5min')->nullable();
            $table->integer('newborn_apgar_10min')->nullable();
            $table->text('newborn_condition')->nullable();
            $table->text('newborn_complications')->nullable();

            // Placenta
            $table->string('placenta_delivery', 50)->nullable(); // Spontaneous, Manual
            $table->boolean('placenta_complete')->default(true);
            $table->text('placenta_notes')->nullable();

            // Blood Loss & Vital Signs
            $table->integer('estimated_blood_loss')->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();

            // Immediate Postpartum Care
            $table->text('postpartum_care')->nullable();
            $table->text('medications_administered')->nullable();
            $table->text('breastfeeding_initiation')->nullable();

            // Discharge Planning
            $table->date('expected_discharge_date')->nullable();
            $table->text('discharge_instructions')->nullable();
            $table->text('follow_up_instructions')->nullable();

            // Documentation
            $table->text('delivery_summary')->nullable();
            $table->text('additional_notes')->nullable();
            $table->json('quality_indicators')->nullable();

            $table->timestamps();

            $table->index(['patient_id', 'delivery_date_time']);
            $table->index('attending_provider_id');
            $table->index('delivering_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_records');
    }
};
