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
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade'); // Staff is a user
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('status')->default('present'); // e.g., present, absent, on_leave
            $table->text('notes')->nullable();
            $table->text('image_proof_check_in')->nullable(); // Path to image proof for check-in
            $table->text('image_proof_check_out')->nullable(); // Path to image proof for check-out
            $table->integer('pin_entered')->nullable(); // Latitude for check-in
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendance');
    }
};
