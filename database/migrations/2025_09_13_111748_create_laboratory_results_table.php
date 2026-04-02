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
        Schema::create('laboratory_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ordered_by')->constrained('users'); // Doctor who ordered the test
            $table->foreignId('performed_by')->nullable()->constrained('users'); // Lab technician
            $table->foreignId('reviewed_by')->nullable()->constrained('users'); // Doctor who reviewed results

            // Test Information
            $table->string('test_name');
            $table->string('test_category'); // hematology, chemistry, microbiology, etc.
            $table->string('test_code')->nullable(); // LOINC or internal code
            $table->text('test_description')->nullable();

            // Sample Information
            $table->enum('sample_type', [
                'blood',
                'urine',
                'stool',
                'sputum',
                'swab',
                'tissue',
                'fluid',
                'other'
            ]);
            $table->string('sample_type_other')->nullable();
            $table->dateTime('sample_collection_date_time');
            $table->string('sample_id')->nullable(); // Lab accession number

            // Test Results
            $table->text('test_result')->nullable();
            $table->string('result_value')->nullable();
            $table->string('result_unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->enum('result_status', [
                'normal',
                'abnormal_high',
                'abnormal_low',
                'critical_high',
                'critical_low',
                'pending',
                'inconclusive'
            ])->default('pending');

            // Test Timing
            $table->dateTime('test_ordered_date_time');
            $table->dateTime('test_performed_date_time')->nullable();
            $table->dateTime('result_available_date_time')->nullable();
            $table->dateTime('result_reviewed_date_time')->nullable();

            // Clinical Information
            $table->text('clinical_indication')->nullable(); // Why test was ordered
            $table->text('interpretation')->nullable(); // Doctor's interpretation
            $table->text('comments')->nullable(); // Additional comments

            // Quality Control
            $table->boolean('qc_passed')->default(true);
            $table->text('qc_notes')->nullable();

            // Billing and Insurance
            $table->decimal('test_cost', 8, 2)->nullable();
            $table->boolean('covered_by_philhealth')->default(false);
            $table->decimal('philhealth_coverage_amount', 8, 2)->nullable();

            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->text('follow_up_instructions')->nullable();
            $table->date('follow_up_date')->nullable();

            // Status and Flags
            $table->enum('test_status', [
                'ordered',
                'sample_collected',
                'in_progress',
                'completed',
                'cancelled',
                'rejected'
            ])->default('ordered');
            $table->boolean('urgent')->default(false);
            $table->boolean('stat')->default(false);

            // Rejection Information (if applicable)
            $table->text('rejection_reason')->nullable();
            $table->dateTime('rejected_date_time')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'test_ordered_date_time']);
            $table->index('test_category');
            $table->index('result_status');
            $table->index('test_status');
            $table->index('sample_collection_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratory_results');
    }
};
