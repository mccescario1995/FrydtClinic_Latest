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
        Schema::create('obstetric_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            // Pregnancy Details
            $table->integer('pregnancy_number'); // 1st, 2nd, 3rd pregnancy, etc.
            $table->date('pregnancy_year'); // Year of pregnancy
            $table->integer('gestational_age_at_delivery')->nullable(); // Weeks at delivery
            $table->enum('pregnancy_outcome', [
                'full_term_delivery',
                'preterm_delivery',
                'spontaneous_abortion',
                'induced_abortion',
                'ectopic_pregnancy',
                'molar_pregnancy',
                'stillbirth'
            ]);

            // Delivery Information
            $table->enum('delivery_type', [
                'vaginal_delivery',
                'cesarean_section',
                'forceps_delivery',
                'vacuum_delivery',
                'breech_delivery'
            ])->nullable();
            $table->enum('delivery_place', [
                'home',
                'health_center',
                'hospital',
                'lying_in_clinic',
                'other'
            ])->nullable();
            $table->string('delivery_place_other')->nullable();

            // Birth Details
            $table->integer('number_of_infants')->default(1);
            $table->enum('infant_sex', ['male', 'female', 'unknown'])->nullable();
            $table->decimal('birth_weight_grams', 6, 2)->nullable();
            $table->decimal('birth_length_cm', 5, 2)->nullable();
            $table->string('apgar_score')->nullable(); // 1min/5min format

            // Complications
            $table->text('maternal_complications')->nullable();
            $table->text('fetal_complications')->nullable();
            $table->text('postpartum_complications')->nullable();

            // Infant Status
            $table->enum('infant_status', [
                'alive',
                'stillborn',
                'died_within_24h',
                'died_within_7days',
                'died_after_7days'
            ])->nullable();
            $table->text('infant_death_cause')->nullable();

            // Medical Interventions
            $table->boolean('received_prenatal_care')->default(false);
            $table->integer('prenatal_visits_count')->nullable();
            $table->text('medications_used')->nullable();
            $table->text('procedures_performed')->nullable();

            // Attendant Information
            $table->string('birth_attendant_name')->nullable();
            $table->enum('birth_attendant_type', [
                'physician',
                'midwife',
                'nurse',
                'traditional_birth_attendant',
                'relative',
                'self',
                'other'
            ])->nullable();

            // Additional Notes
            $table->text('special_notes')->nullable();
            $table->text('follow_up_recommendations')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'pregnancy_number']);
            $table->index('pregnancy_year');
            $table->index('pregnancy_outcome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obstetric_histories');
    }
};
