<?php
namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::firstOrCreate([
            'email' => 'jdoe@email.com'
        ], [
            'name'                => 'John Doe',
            'email'               => 'jdoe@email.com',
            'password'            => bcrypt('user123'),
            'user_type'           => 'patient',
            'status'              => 'active',
            'registration_status' => 'Full',
            'remember_token'      => Str::random(60),
            'email_verified_at'   => now(),
        ]);

        User::firstOrCreate([
            'email' => 'mjane@email.com'
        ], [
            'name'              => 'Mary Jane',
            'email'             => 'mjane@email.com',
            'password'          => bcrypt('user123'),
            'user_type'         => 'patient',
            'status'            => 'active',
            'registration_status' => 'Semi',
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        PatientProfile::firstOrCreate([
            'user_id' => 1
        ], [
            'user_id'                        => 1,
            'address'                        => '123 Main St, Cityville',
            'phone'                          => '093-456-7890',
            'birth_date'                     => '1990-01-01',
            'gender'                         => 'Male',
            'emergency_contact_name'         => 'Jane Doe',
            'emergency_contact_phone'        => '098-765-4321',
            'emergency_contact_relationship' => 'Sister',
            'philhealth_membership'          => 'Member',
            'philhealth_number'              => '1234-5678-9012',
            'image_path'                     => null,
        ]);

        PatientProfile::firstOrCreate([
            'user_id' => 1
        ], [
            'user_id'                        => 2,
            'address'                        => '123 Main St, Cityville',
            'phone'                          => '093-456-7890',
            'birth_date'                     => '1990-01-01',
            'gender'                         => 'Male',
            'emergency_contact_name'         => 'Jane Doe',
            'emergency_contact_phone'        => '098-765-4321',
            'emergency_contact_relationship' => 'Sister',
            'philhealth_membership'          => 'Member',
            'philhealth_number'              => '1234-5678-9012',
            'image_path'                     => null,
        ]);

        User::firstOrCreate([
            'email' => 'admin@email.com'
        ], [
            'name'              => 'Clinic Admin',
            'email'             => 'admin@email.com',
            'password'          => bcrypt('admin123'),
            'user_type'         => 'admin',
            'status'            => 'active',
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'employee@email.com'
        ], [
            'name'              => 'Clinic Employee',
            'email'             => 'employee@email.com',
            'password'          => bcrypt('staff123'),
            'user_type'         => 'employee',
            'status'            => 'active',
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'doctor@email.com'
        ], [
            'name'              => 'Clinic Doctor',
            'email'             => 'doctor@email.com',
            'password'          => bcrypt('doctor123'),
            'user_type'         => 'employee',
            'status'            => 'active',
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        EmployeeProfile::firstOrCreate([
            'employee_id' => 4
        ], [
            'employee_id'     => 4, // Employee user (employee@email.com)
            // 'employee_number' => 'EMP001',
            'position'        => 'Clinic Staff',
            'hire_date'       => '2020-03-01',
            'gender'          => 'Female',
            'phone'           => '092-345-6789',
            'address'         => '123 Clinic St, Healthtown',
            'image_path'      => null,
            'pin'             => 654321,
            'hourly_rate'     => 100.00,
            'employment_type' => 'full_time',
            'status'          => 'active',
        ]);

        EmployeeProfile::firstOrCreate([
            'employee_id' => 5
        ], [
            'employee_id'     => 5, // Doctor user (doctor@email.com)
            // 'employee_number' => 'DOC001',
            'position'        => 'Head Doctor',
            'hire_date'       => '2020-01-15',
            'gender'          => 'Male',
            'phone'           => '091-234-5678',
            'address'         => '456 Clinic Rd, Healthtown',
            'image_path'      => null,
            'pin'             => 123456,
            'hourly_rate'     => 150.00,
            'employment_type' => 'full_time',
            'status'          => 'active',
        ]);
    }
}
