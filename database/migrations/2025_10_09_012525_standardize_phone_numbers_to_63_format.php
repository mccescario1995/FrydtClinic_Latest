<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Standardize phone numbers in patient_profiles table
        $this->standardizePhoneNumbers('patient_profiles');

        // Standardize phone numbers in employee_profiles table
        $this->standardizePhoneNumbers('employee_profiles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration is not reversible as it standardizes data
        // In a production environment, you would need a backup strategy
    }

    /**
     * Standardize phone numbers in a given table
     */
    private function standardizePhoneNumbers(string $tableName): void
    {
        $records = DB::table($tableName)->whereNotNull('phone')->get();

        foreach ($records as $record) {
            $standardizedPhone = $this->formatPhoneNumber($record->phone);

            if ($standardizedPhone !== $record->phone) {
                DB::table($tableName)
                    ->where('id', $record->id)
                    ->update(['phone' => $standardizedPhone]);
            }
        }
    }

    /**
     * Format phone number to 63xxxxxxxxx format
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Handle different input formats
        if (str_starts_with($phone, '63') && strlen($phone) === 12) {
            // Already in correct format: 63xxxxxxxxx
            return $phone;
        } elseif (str_starts_with($phone, '+63') && strlen($phone) === 13) {
            // Remove + from +63xxxxxxxxx to get 63xxxxxxxxx
            return substr($phone, 1);
        } elseif (str_starts_with($phone, '0') && strlen($phone) === 11) {
            // Convert 0xxxxxxxxx to 63xxxxxxxxx
            return '63' . substr($phone, 1);
        } elseif (strlen($phone) === 10 && str_starts_with($phone, '9')) {
            // Convert 9xxxxxxxxx to 63xxxxxxxxx
            return '63' . $phone;
        } elseif (strlen($phone) === 11 && str_starts_with($phone, '9')) {
            // Handle 09xxxxxxxxx format (11 digits starting with 9 after 0)
            return '63' . substr($phone, 1);
        } else {
            // For any other format, try to add 63 prefix if it doesn't have it
            if (!str_starts_with($phone, '63')) {
                $phone = '63' . $phone;
            }
            return $phone;
        }
    }
};
