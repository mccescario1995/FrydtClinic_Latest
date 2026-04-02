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
        Schema::table('prescriptions', function (Blueprint $table) {
            // Add purchase_location enum
            $table->enum('purchase_location', ['clinic', 'outside'])->nullable()->after('status');

            // Update status enum to include 'external_purchase'
            $table->enum('status', [
                'prescribed',
                'partially_dispensed',
                'fully_dispensed',
                'external_purchase',
                'cancelled'
            ])->default('prescribed')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('purchase_location');

            // Revert status enum
            $table->enum('status', [
                'prescribed',
                'partially_dispensed',
                'fully_dispensed',
                'cancelled'
            ])->default('prescribed')->change();
        });
    }
};
