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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users');

            // Document Information
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('document_type', [
                'medical_record',
                'lab_result',
                'imaging_report',
                'prescription',
                'consent_form',
                'discharge_summary',
                'referral_letter',
                'insurance_document',
                'identification',
                'other'
            ]);
            $table->enum('category', [
                'prenatal',
                'labor_delivery',
                'postnatal',
                'pediatric',
                'general_medicine',
                'surgical',
                'emergency',
                'administrative',
                'other'
            ]);

            // File Information
            $table->string('file_name');
            $table->string('original_file_name');
            $table->string('file_path');
            $table->string('file_url')->nullable();
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->string('file_extension');

            // Document Metadata
            $table->date('document_date')->nullable();
            $table->string('document_number')->nullable(); // Reference number
            $table->string('issuing_authority')->nullable();
            $table->date('expiration_date')->nullable();

            // Access Control
            $table->enum('access_level', [
                'public',
                'patient_only',
                'staff_only',
                'restricted'
            ])->default('patient_only');
            $table->boolean('is_confidential')->default(false);
            $table->text('access_restrictions')->nullable();

            // Version Control
            $table->string('version')->default('1.0');
            $table->foreignId('parent_document_id')->nullable()->constrained('documents');
            $table->text('change_log')->nullable();

            // Digital Signature and Verification
            $table->boolean('is_digitally_signed')->default(false);
            $table->text('digital_signature')->nullable();
            $table->dateTime('signed_at')->nullable();
            $table->foreignId('signed_by')->nullable()->constrained('users');

            // Status and Workflow
            $table->enum('status', [
                'active',
                'archived',
                'deleted',
                'pending_review',
                'rejected'
            ])->default('active');
            $table->text('status_notes')->nullable();

            // Review and Approval
            $table->boolean('requires_review')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Retention and Compliance
            $table->integer('retention_years')->nullable();
            $table->date('retention_expiry_date')->nullable();
            $table->text('retention_policy')->nullable();
            $table->text('legal_hold_reason')->nullable();

            // Tags and Search
            $table->json('tags')->nullable();
            $table->text('keywords')->nullable();

            // Audit Trail
            $table->integer('download_count')->default(0);
            $table->dateTime('last_accessed_at')->nullable();
            $table->foreignId('last_accessed_by')->nullable()->constrained('users');

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'document_type']);
            $table->index('uploaded_by');
            $table->index('document_date');
            $table->index('status');
            $table->index('expiration_date');
            $table->index('access_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
