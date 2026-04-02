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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('inventory_category_id');
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });

        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->integer('quantity_moved'); // Positive for 'in', negative for 'out'
            $table->string('movement_type'); // e.g., 'stock_in', 'stock_out', 'sale', 'loss'
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users'); // Optional, to track who made the change
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('inventory_categories');
        Schema::dropIfExists('inventory_movements');
    }
};
