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
            // Add GCash fields
            $table->text('gcash_qr_data')->nullable()->after('paypal_order_id');
            $table->string('gcash_reference')->nullable()->after('gcash_qr_data');

            // Update payment_method enum to include gcash
            $table->enum('payment_method', ['paypal', 'gcash', 'cash', 'card'])
                  ->default('paypal')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove GCash fields
            $table->dropColumn(['gcash_qr_data', 'gcash_reference']);

            // Revert payment_method enum
            $table->enum('payment_method', ['paypal', 'cash', 'card'])
                  ->default('paypal')
                  ->change();
        });
    }
};
