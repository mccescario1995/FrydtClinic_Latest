<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // SMS Settings
            [
                'key' => 'admin_sms_number',
                'value' => env('ADMIN_SMS_NUMBER', ''),
                'type' => 'string',
                'group' => 'sms',
                'description' => 'Admin SMS notification number',
            ],
            [
                'key' => 'sms_enabled',
                'value' => env('SEMAPHORE_API_KEY') ? '1' : '0',
                'type' => 'boolean',
                'group' => 'sms',
                'description' => 'Enable SMS notifications',
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
