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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('cascade');
            $table->string('payment_reference')->unique();   // Our internal reference
            $table->string('paypal_payment_id')->nullable(); // PayPal payment ID
            $table->string('paypal_payer_id')->nullable();   // PayPal payer ID
            $table->string('paypal_order_id')->nullable();   // PayPal order ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PHP');
            $table->enum('payment_method', ['paypal', 'cash', 'card'])->default('paypal');
            $table->enum('status', ['pending', 'completed', 'successful', 'failed', 'cancelled', 'refunded', 'awaiting_approval'])
                ->default('pending');
            $table->text('description')->nullable();
            $table->json('paypal_response')->nullable(); // Store PayPal response data
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
