<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $roles = ['Admin', 'Patient', 'Employee', 'Doctor'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $patient = User::where('email', 'jdoe@email.com')->first();
        $patientRole = Role::where('name', 'Patient')->first();
        $patient->assignRole($patientRole);

        $patient = User::where('email', 'mjane@email.com')->first();
        $patientRole = Role::where('name', 'Patient')->first();
        $patient->assignRole($patientRole);

        $admin = User::where('email', 'admin@email.com')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $admin->assignRole($adminRole);

        $staff = User::where('email', 'employee@email.com')->first();
        $staffRole = Role::where('name', 'Employee')->first();
        $staff->assignRole($staffRole);

        $doctor = User::where('email', 'doctor@email.com')->first();
        $doctorRole = Role::where('name', 'Doctor')->first();
        $doctor->assignRole($doctorRole);

        // Assign roles to additional employees created by TimeSlotSeeder
        $additionalEmployees = [
            'dr.santos@email.com' => 'Doctor',
            'nurse.jennifer@email.com' => 'Employee',
            'therapist.mark@email.com' => 'Employee',
        ];

        foreach ($additionalEmployees as $email => $roleName) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $role = Role::where('name', $roleName)->first();
                $user->assignRole($role);
            }
        }
    }
}
