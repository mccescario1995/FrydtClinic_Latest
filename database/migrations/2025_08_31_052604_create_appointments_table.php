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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->dateTime('appointment_datetime');
            $table->dateTime('end_time');
            $table->integer('duration_in_minutes');         // Copied from service for historical accuracy
            $table->string('status')->default('scheduled'); // e.g., scheduled, completed, cancelled
            $table->string('purpose')->nullable();           // Reason for visit
            $table->enum('type', ['new_patient', 'follow_up', 'emergency_consult'])->default('new_patient'); // Type of appointment
            $table->text('patient_notes')->nullable();
            $table->text('employee_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
