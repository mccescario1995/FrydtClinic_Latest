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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->default('staff'); // staff, patient, system
            $table->string('action'); // create, update, delete, view, login, logout
            $table->string('model_type')->nullable(); // App\Models\User, App\Models\PatientProfile, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description');
            $table->json('old_values')->nullable(); // For tracking changes
            $table->json('new_values')->nullable(); // For tracking changes
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
