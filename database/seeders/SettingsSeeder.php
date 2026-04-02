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
            // SMS Settings - Read from .env if available
            [
                'key' => 'iprogsms_token',
                'value' => trim(env('IPROGSMS_TOKEN', ''), '"'),
                'type' => 'string',
                'group' => 'sms',
                'description' => 'iProgSMS API Token',
            ],
            [
                'key' => 'iprogsms_url',
                'value' => env('IPROGSMS_URL', 'https://www.iprogsms.com/api/v1/sms_messages'),
                'type' => 'string',
                'group' => 'sms',
                'description' => 'iProgSMS API URL',
            ],
            [
                'key' => 'admin_sms_number',
                'value' => env('ADMIN_SMS_NUMBER', ''),
                'type' => 'string',
                'group' => 'sms',
                'description' => 'Admin SMS notification number',
            ],
            [
                'key' => 'sms_enabled',
                'value' => env('IPROGSMS_TOKEN') ? '1' : '0',
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
