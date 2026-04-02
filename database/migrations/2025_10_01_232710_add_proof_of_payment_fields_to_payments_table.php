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
        Schema::table('payments', function (Blueprint $table) {
            // Add proof of payment fields
            $table->string('proof_of_payment_path')->nullable()->after('gcash_reference');
            $table->text('proof_of_payment_notes')->nullable()->after('proof_of_payment_path');
            $table->timestamp('proof_uploaded_at')->nullable()->after('proof_of_payment_notes');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('proof_uploaded_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Update payment status enum to include 'successful'
            $table->enum('status', ['pending', 'completed', 'successful', 'failed', 'cancelled', 'refunded', 'awaiting_approval'])
                  ->default('pending')
                  ->change();

            // Add partial payment fields
            $table->decimal('paid_amount', 10, 2)->default(0)->after('amount');
            $table->decimal('remaining_balance', 10, 2)->default(0)->after('paid_amount');
            $table->boolean('is_partial_payment')->default(false)->after('remaining_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'proof_of_payment_path',
                'proof_of_payment_notes',
                'proof_uploaded_at',
                'approved_by',
                'approved_at',
                'paid_amount',
                'remaining_balance',
                'is_partial_payment'
            ]);

            // Revert payment status enum
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'refunded'])
                  ->default('pending')
                  ->change();
        });
    }
};
