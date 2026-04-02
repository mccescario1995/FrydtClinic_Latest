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
        Schema::create('labor_delivery_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('attending_physician_id')->nullable()->constrained('users');
            $table->foreignId('midwife_id')->nullable()->constrained('users');
            $table->foreignId('nurse_id')->nullable()->constrained('users');

            // Admission Information
            $table->dateTime('admission_date_time');
            $table->string('admission_reason')->nullable();
            $table->text('chief_complaint')->nullable();

            // Labor Onset
            $table->dateTime('labor_onset_date_time')->nullable();
            $table->boolean('rupture_of_membranes')->default(false);
            $table->dateTime('membrane_rupture_date_time')->nullable();
            $table->string('amniotic_fluid_color')->nullable();
            $table->string('amniotic_fluid_amount')->nullable();

            // Labor Progress
            $table->integer('cervical_dilation_cm')->nullable();
            $table->integer('cervical_effacement_percent')->nullable();
            $table->string('fetal_station')->nullable();
            $table->integer('contraction_frequency_min')->nullable();
            $table->integer('contraction_duration_sec')->nullable();
            $table->string('contraction_intensity')->nullable();

            // Fetal Monitoring
            $table->integer('fetal_heart_rate')->nullable();
            $table->string('fetal_heart_rate_pattern')->nullable();
            $table->string('fetal_position')->nullable();
            $table->string('fetal_presentation')->nullable();

            // Maternal Vital Signs
            $table->decimal('maternal_blood_pressure_systolic', 5, 2)->nullable();
            $table->decimal('maternal_blood_pressure_diastolic', 5, 2)->nullable();
            $table->integer('maternal_pulse_rate')->nullable();
            $table->decimal('maternal_temperature_celsius', 4, 2)->nullable();
            $table->integer('maternal_respiratory_rate')->nullable();

            // Delivery Information
            $table->dateTime('delivery_date_time')->nullable();
            $table->enum('delivery_type', [
                'spontaneous_vaginal',
                'assisted_vaginal',
                'cesarean_section',
                'forceps',
                'vacuum',
                'breech'
            ])->nullable();
            $table->string('delivery_position')->nullable();
            $table->text('delivery_notes')->nullable();

            // Placental Delivery
            $table->dateTime('placental_delivery_date_time')->nullable();
            $table->boolean('placenta_complete')->default(false);
            $table->string('placental_weight_grams')->nullable();
            $table->text('placental_notes')->nullable();

            // Perineal Care
            $table->enum('perineal_condition', [
                'intact',
                'first_degree_tear',
                'second_degree_tear',
                'third_degree_tear',
                'fourth_degree_tear',
                'episiotomy'
            ])->nullable();
            $table->text('perineal_repair_notes')->nullable();

            // Immediate Postpartum Care
            $table->decimal('postpartum_blood_loss_ml', 6, 2)->nullable();
            $table->boolean('oxytocin_administered')->default(false);
            $table->decimal('oxytocin_dosage_units', 6, 2)->nullable();
            $table->text('postpartum_medications')->nullable();

            // Newborn Information (for linking to newborn records)
            $table->integer('number_of_infants_delivered')->default(1);
            $table->text('newborn_immediate_care')->nullable();

            // Complications
            $table->text('maternal_complications')->nullable();
            $table->text('fetal_complications')->nullable();
            $table->text('delivery_complications')->nullable();

            // Interventions
            $table->text('medical_interventions')->nullable();
            $table->text('procedures_performed')->nullable();

            // Discharge Planning
            $table->dateTime('discharge_date_time')->nullable();
            $table->text('discharge_instructions')->nullable();
            $table->text('follow_up_instructions')->nullable();

            // Documentation
            $table->text('labor_progress_notes')->nullable();
            $table->text('delivery_summary')->nullable();
            $table->text('physician_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'admission_date_time']);
            $table->index('delivery_date_time');
            $table->index('discharge_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_delivery_records');
    }
};
