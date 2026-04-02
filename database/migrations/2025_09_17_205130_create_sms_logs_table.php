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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->text('message');
            $table->string('sms_type')->nullable(); // appointment, payment, lab_result, reminder, test
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->string('twilio_sid')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // patient who received SMS
            $table->unsignedBigInteger('sent_by')->nullable(); // admin/employee who triggered SMS
            $table->json('metadata')->nullable(); // additional data like appointment_id, payment_id, etc.
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['phone_number', 'status']);
            $table->index(['sms_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
