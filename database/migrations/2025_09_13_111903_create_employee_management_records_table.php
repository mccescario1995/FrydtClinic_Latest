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
        Schema::create('employee_management_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');

            // Record Type
            $table->enum('record_type', [
                'certification',
                'training',
                'performance_evaluation',
                'disciplinary_action',
                'achievement',
                'license',
                'vaccination',
                'health_screening',
                'other'
            ]);

            // Record Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('issuing_authority')->nullable(); // For certifications/licenses
            $table->string('reference_number')->nullable(); // Certificate/license number

            // Date Information
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('review_date')->nullable();

            // Status and Validity
            $table->enum('status', [
                'active',
                'expired',
                'revoked',
                'pending',
                'completed',
                'in_progress'
            ])->default('active');

            // Performance/Skill Assessment (for evaluations)
            $table->integer('rating')->nullable(); // 1-5 scale
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals_objectives')->nullable();

            // Training Information
            $table->integer('training_hours')->nullable();
            $table->string('training_provider')->nullable();
            $table->decimal('training_cost', 8, 2)->nullable();

            // Disciplinary Information
            $table->enum('disciplinary_type', [
                'verbal_warning',
                'written_warning',
                'suspension',
                'termination',
                'other'
            ])->nullable();
            $table->text('disciplinary_reason')->nullable();
            $table->text('disciplinary_action_taken')->nullable();

            // Achievement Information
            $table->text('achievement_details')->nullable();
            $table->text('recognition_given')->nullable();

            // Health and Safety
            $table->text('health_conditions')->nullable();
            $table->text('restrictions_limitations')->nullable();
            $table->text('emergency_contact_updates')->nullable();

            // Documentation
            $table->string('document_path')->nullable(); // File path for certificates, etc.
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable(); // For HR internal use

            // Review and Approval
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->dateTime('reviewed_at')->nullable();
            $table->text('reviewer_comments')->nullable();

            // Renewal Reminders
            $table->boolean('requires_renewal')->default(false);
            $table->integer('renewal_reminder_days')->nullable(); // Days before expiry to send reminder

            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'record_type']);
            $table->index('status');
            $table->index('expiry_date');
            $table->index('review_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_management_records');
    }
};
