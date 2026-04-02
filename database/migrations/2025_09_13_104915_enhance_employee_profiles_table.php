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
        Schema::table('employee_profiles', function (Blueprint $table) {
            // Add missing fields from ERD that don't already exist
            // $table->string('employee_number')->unique()->after('id');
            $table->decimal('hourly_rate', 8, 2)->default(0)->after('pin');
            $table->enum('employment_type', ['full_time', 'part_time', 'contractual'])->default('full_time')->after('hourly_rate');
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active')->after('employment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn([
                // 'employee_number',
                'hourly_rate',
                'employment_type',
                'status'
            ]);
        });
    }
};
