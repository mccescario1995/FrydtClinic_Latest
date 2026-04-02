<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Backpack view namespace
        $this->loadViewsFrom(resource_path('views/backpack'), 'backpack');

        // Register custom validation rules
        $this->registerCustomValidators();

        // Schema optimizations will be handled by Backpack caching config
    }

    /**
     * Normalize phone number to standard format (static method)
     */
    public static function normalizePhoneNumberStatic($phone)
    {
        if (empty($phone)) {
            return '';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Convert to standard format (63xxxxxxxxx)
        if (str_starts_with($phone, '+63') && strlen($phone) === 13) {
            return substr($phone, 1); // Remove + to get 63xxxxxxxxx
        } elseif (str_starts_with($phone, '0') && strlen($phone) === 11) {
            return '63' . substr($phone, 1); // Convert 0xxxxxxxxx to 63xxxxxxxxx
        } elseif (strlen($phone) === 10 && str_starts_with($phone, '9')) {
            return '63' . $phone; // Convert 9xxxxxxxxx to 63xxxxxxxxx
        } elseif (strlen($phone) === 11 && str_starts_with($phone, '9')) {
            return '63' . substr($phone, 1); // Convert 09xxxxxxxxx to 63xxxxxxxxx
        }

        return $phone; // Return as-is if already in correct format
    }

    /**
     * Register custom validation rules
     */
    private function registerCustomValidators(): void
    {
        // Philippine phone number validation
        \Illuminate\Support\Facades\Validator::extend('philippine_phone', function ($attribute, $value, $parameters, $validator) {
            if (empty($value)) {
                return true; // Allow empty values
            }

            // Remove all non-numeric characters
            $phone = preg_replace('/\D/', '', $value);

            // Check if it matches any of the valid Philippine phone number formats
            return (str_starts_with($phone, '63') && strlen($phone) === 12) || // 63xxxxxxxxx
                   (str_starts_with($phone, '+63') && strlen($phone) === 13) || // +63xxxxxxxxx
                   (str_starts_with($phone, '0') && strlen($phone) === 11) || // 0xxxxxxxxx
                   (strlen($phone) === 10 && str_starts_with($phone, '9')) || // 9xxxxxxxxx
                   (strlen($phone) === 11 && str_starts_with($phone, '9')); // 09xxxxxxxxx
        });

        // Custom error message for Philippine phone validation
        \Illuminate\Support\Facades\Validator::replacer('philippine_phone', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute must be a valid Philippine mobile number (e.g., 639123456789, +639123456789, 09123456789, 9123456789, or 09123456789).');
        });

        // Unique phone validation for patient profiles
        \Illuminate\Support\Facades\Validator::extend('unique_patient_phone', function ($attribute, $value, $parameters, $validator) {
            $userId = $parameters[0] ?? null;

            // Normalize phone number for comparison
            $normalizedPhone = self::normalizePhoneNumberStatic($value);

            $query = \App\Models\PatientProfile::where('phone', $normalizedPhone);

            if ($userId) {
                $query->where('user_id', '!=', $userId);
            }

            return $query->count() === 0;
        });

        // Unique phone validation for employee profiles
        \Illuminate\Support\Facades\Validator::extend('unique_employee_phone', function ($attribute, $value, $parameters, $validator) {
            $userId = $parameters[0] ?? null;

            // Normalize phone number for comparison
            $normalizedPhone = self::normalizePhoneNumberStatic($value);

            $query = \App\Models\EmployeeProfile::where('phone', $normalizedPhone);

            if ($userId) {
                $query->where('employee_id', '!=', $userId);
            }

            return $query->count() === 0;
        });

        // Custom error messages for unique phone validations
        \Illuminate\Support\Facades\Validator::replacer('unique_patient_phone', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'This phone number is already registered to another patient.');
        });

        \Illuminate\Support\Facades\Validator::replacer('unique_employee_phone', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'This phone number is already registered to another employee.');
        });
    }
}
