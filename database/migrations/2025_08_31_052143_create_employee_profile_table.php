<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade'); // Staff is a user
            $table->string('position')->nullable();
            $table->string('hire_date')->default(Carbon::now());
            $table->string('gender')->nuillable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('pin')->unique()->nullable(); // Employee PIN for attendance
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
