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
        Schema::table('patient_profiles', function (Blueprint $table) {
            // Add missing fields from ERD that don't already exist
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated'])->nullable()->after('image_path');
            $table->string('occupation')->nullable()->after('civil_status');
            $table->string('religion')->nullable()->after('occupation');
            $table->string('blood_type')->nullable()->after('religion');
            $table->string('barangay_captain')->nullable()->after('blood_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'civil_status',
                'occupation',
                'religion',
                'blood_type',
                'barangay_captain'
            ]);
        });
    }
};
