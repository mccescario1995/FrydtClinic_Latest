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
        Schema::create('package_service', function (Blueprint $table) {
            $table->foreignId('package_id')->constrained('services');
            $table->foreignId('service_id')->constrained('services');
            $table->decimal('discounted_service_price', 8, 2)->nullable(); // New column for discounted price
            $table->primary(['package_id', 'service_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_service');
    }
};
