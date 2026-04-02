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
        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            // Billing Information
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->text('billing_description')->nullable();

            // Financial Amounts
            $table->decimal('subtotal_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('philhealth_coverage', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);

            // Payment Information
            $table->enum('payment_status', [
                'unpaid',
                'partially_paid',
                'paid',
                'overdue',
                'cancelled',
                'refunded'
            ])->default('unpaid');
            $table->enum('payment_method', [
                'cash',
                'credit_card',
                'debit_card',
                'bank_transfer',
                'check',
                'philhealth',
                'insurance',
                'paypal',
                'gcash',
                'other'
            ])->nullable();
            $table->string('payment_reference')->nullable(); // Transaction ID, check number, etc.

            // Insurance Information
            $table->boolean('has_insurance')->default(false);
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->decimal('insurance_coverage_amount', 10, 2)->default(0);
            $table->string('insurance_claim_number')->nullable();

            // PhilHealth Information
            $table->boolean('philhealth_member')->default(false);
            $table->string('philhealth_number')->nullable();
            $table->decimal('philhealth_benefit_amount', 10, 2)->default(0);

            // Service Details (JSON for multiple services)
            $table->json('services_rendered')->nullable(); // Array of service IDs and details

            // Billing Period
            $table->date('service_start_date')->nullable();
            $table->date('service_end_date')->nullable();

            // Responsible Party
            $table->string('responsible_party_name')->nullable();
            $table->string('responsible_party_relationship')->nullable();
            $table->string('responsible_party_contact')->nullable();

            // Status and Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->text('billing_notes')->nullable();
            $table->text('collection_notes')->nullable();

            // Audit Trail
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('last_updated_by')->nullable()->constrained('users');

            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'invoice_date']);
            $table->index('payment_status');
            $table->index('due_date');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing');
    }
};
