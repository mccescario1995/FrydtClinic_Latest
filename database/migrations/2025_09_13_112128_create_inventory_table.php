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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();

            // Item Information
            $table->string('name');
            $table->string('item_code')->unique();
            $table->text('description')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model_number')->nullable();
            $table->string('serial_number')->nullable();

            // Classification
            $table->enum('item_type', [
                'medical_supply',
                'equipment',
                'medication',
                'consumable',
                'durable_medical_equipment',
                'laboratory_supply',
                'office_supply',
                'other'
            ])->default('medical_supply');
            $table->enum('category', [
                'surgical_instruments',
                'diagnostic_equipment',
                'medications',
                'bandages_dressings',
                'gloves_masks',
                'syringes_needles',
                'laboratory_supplies',
                'office_supplies',
                'furniture',
                'other'
            ])->default('other');

            // Inventory Tracking
            $table->integer('current_quantity');
            $table->integer('minimum_quantity')->default(0); // Reorder point
            $table->integer('maximum_quantity')->nullable(); // Maximum stock level
            $table->string('unit_of_measure'); // pieces, boxes, bottles, etc.

            // Location and Storage
            $table->string('storage_location')->nullable();
            $table->string('room_number')->nullable();
            $table->string('cabinet_drawer')->nullable();
            $table->enum('storage_conditions', [
                'room_temperature',
                'refrigerated',
                'frozen',
                'controlled_room',
                'dark_place',
                'other'
            ])->nullable();

            // Financial Information
            $table->decimal('unit_cost', 8, 2)->nullable();
            $table->decimal('selling_price', 8, 2)->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();

            // Regulatory and Compliance
            $table->date('expiry_date')->nullable();
            $table->string('batch_lot_number')->nullable();
            $table->string('fda_registration_number')->nullable();
            $table->boolean('requires_prescription')->default(false);
            $table->text('regulatory_notes')->nullable();

            // Status and Maintenance
            $table->enum('status', [
                'active',
                'inactive',
                'discontinued',
                'under_maintenance',
                'out_of_stock',
                'expired'
            ])->default('active');
            $table->date('last_inventory_check')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('maintenance_notes')->nullable();

            // Usage Tracking
            $table->integer('usage_count')->default(0);
            $table->date('last_used_date')->nullable();
            $table->text('usage_notes')->nullable();

            // Alerts and Notifications
            $table->boolean('low_stock_alert')->default(true);
            $table->boolean('expiry_alert')->default(true);
            $table->integer('alert_before_expiry_days')->default(30);

            // Documentation
            $table->string('manual_document_path')->nullable();
            $table->text('special_handling_instructions')->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('item_type');
            $table->index('category');
            $table->index('status');
            $table->index('expiry_date');
            $table->index('item_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
