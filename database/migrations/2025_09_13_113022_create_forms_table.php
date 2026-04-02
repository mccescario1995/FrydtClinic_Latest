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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            // Form Information
            $table->string('form_name');
            $table->string('form_type'); // consent, intake, assessment, discharge, etc.
            $table->string('form_code')->nullable(); // Unique identifier
            $table->text('description')->nullable();

            // Form Content and Structure
            $table->json('form_fields')->nullable(); // Dynamic form fields structure
            $table->json('form_data')->nullable(); // Patient responses/data
            $table->text('form_template')->nullable(); // HTML template for the form

            // Form Status and Workflow
            $table->enum('status', [
                'draft',
                'pending_review',
                'completed',
                'signed',
                'archived'
            ])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // Assignment and Responsibility
            $table->foreignId('assigned_to')->nullable()->constrained('users'); // Staff member responsible
            $table->foreignId('completed_by')->nullable()->constrained('users');
            $table->dateTime('assigned_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->dateTime('due_date')->nullable();

            // Digital Signatures
            $table->boolean('requires_signature')->default(false);
            $table->text('digital_signature')->nullable(); // Base64 encoded signature
            $table->dateTime('signed_date')->nullable();
            $table->string('signature_method')->nullable(); // digital, wet_signature, etc.

            // Document Management
            $table->string('file_path')->nullable(); // Path to generated PDF
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->string('mime_type')->nullable();

            // Version Control
            $table->string('version')->default('1.0');
            $table->text('change_log')->nullable();
            $table->foreignId('parent_form_id')->nullable()->constrained('forms'); // For form revisions

            // Compliance and Legal
            $table->boolean('is_confidential')->default(false);
            $table->text('retention_policy')->nullable();
            $table->date('expiration_date')->nullable();
            $table->text('legal_notes')->nullable();

            // Integration and Automation
            $table->boolean('auto_generate')->default(false);
            $table->string('trigger_event')->nullable(); // What triggers form creation
            $table->json('automation_rules')->nullable();

            // Review and Approval
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_date')->nullable();
            $table->text('approval_notes')->nullable();

            // Communication
            $table->boolean('send_to_patient')->default(false);
            $table->dateTime('sent_date')->nullable();
            $table->text('patient_instructions')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'form_type']);
            $table->index('status');
            $table->index('assigned_to');
            $table->index('due_date');
            $table->index('form_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
