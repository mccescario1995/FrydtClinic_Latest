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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Setting key (e.g., 'iprogsms_token', 'admin_sms_number')
            $table->text('value')->nullable(); // Setting value
            $table->string('type')->default('string'); // Setting type (string, boolean, json, etc.)
            $table->string('group')->default('general'); // Setting group (sms, general, etc.)
            $table->string('description')->nullable(); // Human readable description
            $table->timestamps();

            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
