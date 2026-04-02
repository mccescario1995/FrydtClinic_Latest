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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('pin')->nullable()->unique()->after('password');
            $table->boolean('pin_verified')->default(false)->after('pin');
            $table->timestamp('pin_verified_at')->nullable()->after('pin_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pin', 'pin_verified', 'pin_verified_at']);
        });
    }
};
