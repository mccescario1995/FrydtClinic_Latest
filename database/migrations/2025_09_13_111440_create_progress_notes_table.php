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
        Schema::create('progress_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users'); // Doctor, nurse, or midwife

            // Note Information
            $table->dateTime('note_date');
            $table->string('note_type'); // SOAP, progress, consultation, etc.
            $table->string('encounter_type')->nullable(); // prenatal, postnatal, general, etc.

            // SOAP Format
            $table->text('subjective')->nullable(); // Patient's reported symptoms
            $table->text('objective')->nullable(); // Clinical findings
            $table->text('assessment')->nullable(); // Diagnosis and clinical impression
            $table->text('plan')->nullable(); // Treatment plan and follow-up

            // Progress Assessment
            $table->enum('patient_condition', [
                'stable',
                'improving',
                'worsening',
                'critical',
                'unchanged'
            ])->nullable();
            $table->text('progress_summary')->nullable();

            // Symptoms and Complaints
            $table->text('current_symptoms')->nullable();
            $table->text('chief_complaint')->nullable();
            $table->text('other_complaints')->nullable();

            // Physical Examination Findings
            $table->text('general_appearance')->nullable();
            $table->text('systematic_examination')->nullable();
            $table->text('abnormal_findings')->nullable();

            // Diagnosis and Problems
            $table->text('primary_diagnosis')->nullable();
            $table->text('secondary_diagnoses')->nullable();
            $table->text('differential_diagnosis')->nullable();

            // Treatment and Interventions
            $table->text('current_treatments')->nullable();
            $table->text('medication_changes')->nullable();
            $table->text('procedures_performed')->nullable();

            // Patient Response
            $table->enum('treatment_response', [
                'excellent',
                'good',
                'fair',
                'poor',
                'adverse_reaction'
            ])->nullable();
            $table->text('patient_response_notes')->nullable();

            // Counseling and Education
            $table->text('patient_education')->nullable();
            $table->text('counseling_provided')->nullable();
            $table->text('lifestyle_advice')->nullable();

            // Follow-up and Recommendations
            $table->date('next_visit_date')->nullable();
            $table->text('follow_up_instructions')->nullable();
            $table->text('referrals')->nullable();

            // Special Considerations
            $table->boolean('requires_special_attention')->default(false);
            $table->text('special_considerations')->nullable();
            $table->text('alerts_warnings')->nullable();

            // Documentation
            $table->text('additional_notes')->nullable();
            $table->text('confidential_notes')->nullable(); // For internal use only

            // Status and Review
            $table->boolean('reviewed_by_supervisor')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->dateTime('reviewed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'note_date']);
            $table->index('author_id');
            $table->index('note_type');
            $table->index('encounter_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_notes');
    }
};
