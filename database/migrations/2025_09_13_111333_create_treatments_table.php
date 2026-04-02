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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('prescribed_by')->constrained('users'); // Doctor or midwife
            $table->foreignId('administered_by')->nullable()->constrained('users'); // Nurse or midwife

            // Treatment Information
            $table->string('treatment_type'); // medication, procedure, therapy, etc.
            $table->string('treatment_name');
            $table->text('treatment_description')->nullable();

            // Prescription Details
            $table->string('generic_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('route')->nullable(); // oral, IV, IM, topical, etc.
            $table->integer('duration_days')->nullable();
            $table->integer('quantity_prescribed')->nullable();

            // Administration Details
            $table->dateTime('prescribed_date');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('administered_date')->nullable();
            $table->string('administration_site')->nullable();

            // Treatment Status
            $table->enum('status', [
                'prescribed',
                'started',
                'ongoing',
                'completed',
                'discontinued',
                'held'
            ])->default('prescribed');

            // Indications and Purpose
            $table->text('indication')->nullable(); // Why prescribed
            $table->text('treatment_goal')->nullable();

            // Response and Effectiveness
            $table->enum('patient_response', [
                'excellent',
                'good',
                'fair',
                'poor',
                'adverse_reaction'
            ])->nullable();
            $table->text('response_notes')->nullable();

            // Side Effects and Complications
            $table->text('side_effects')->nullable();
            $table->text('adverse_reactions')->nullable();
            $table->text('complications')->nullable();

            // Monitoring and Follow-up
            $table->text('monitoring_parameters')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->text('follow_up_instructions')->nullable();

            // Cost and Billing
            $table->decimal('unit_cost', 8, 2)->nullable();
            $table->decimal('total_cost', 8, 2)->nullable();
            $table->boolean('covered_by_philhealth')->default(false);

            // Documentation
            $table->text('special_instructions')->nullable();
            $table->text('nursing_notes')->nullable();
            $table->text('physician_notes')->nullable();

            // Emergency and Priority
            $table->enum('priority', ['routine', 'urgent', 'stat'])->default('routine');
            $table->boolean('requires_monitoring')->default(false);

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'prescribed_date']);
            $table->index('treatment_type');
            $table->index('status');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
