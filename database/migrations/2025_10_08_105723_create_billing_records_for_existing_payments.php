<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Billing;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create billing records for existing approved payments that don't have billing records yet.
     */
    public function up(): void
    {
        // Get approved payments that don't have billing records
        $approvedPayments = Payment::whereIn('status', ['successful', 'completed'])
            ->whereDoesntHave('billingRecords')
            ->get();

        $created = 0;
        foreach ($approvedPayments as $payment) {
            try {
                // Use the updated createBillingRecord method which now properly checks PhilHealth membership
                $payment->createBillingRecord(1); // Use admin ID 1 as default
                $created++;
            } catch (\Exception $e) {
                // Log error but continue with other payments
                \Illuminate\Support\Facades\Log::error('Failed to create billing record for payment ' . $payment->payment_reference . ': ' . $e->getMessage());
            }
        }

        // Log the results
        \Illuminate\Support\Facades\Log::info("Migration completed: Created {$created} billing records for existing approved payments");
    }

    /**
     * Reverse the migrations.
     * Note: This migration creates data, so reversing it would delete billing records.
     * Only reverse if you're absolutely sure this is what you want.
     */
    public function down(): void
    {
        // This is a data migration, so reversing it would delete the created billing records
        // Uncomment the following lines only if you want to remove the billing records created by this migration

        /*
        $billingRecords = Billing::where('billing_notes', 'like', '%Automatically generated from payment approval%')
            ->where('created_at', '>=', '2025-10-08 10:57:23') // Migration creation timestamp
            ->delete();

        \Illuminate\Support\Facades\Log::info("Migration reversed: Deleted {$billingRecords} billing records");
        */
    }
};
