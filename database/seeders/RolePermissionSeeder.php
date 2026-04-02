<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view dashboard',
            'manage users',
            'manage roles',
            'view pages',
            'edit pages',
            'delete pages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminPermissions = Permission::whereIn('name', [
            'view dashboard',
            'manage users',
            'manage roles',
            'view pages',
            'edit pages',
            'delete pages',
        ])->get();

        $staffPermissions = Permission::whereIn('name', [
            'view dashboard',
            'view pages',
            'edit pages',
        ])->get();

        $patientPermissions = Permission::whereIn('name', [
            'view dashboard',
            'view pages',
        ])->get();

        $adminRole = Role::where('name', 'Admin')->first();
        $adminRole->givePermissionTo($adminPermissions);

        $staffRole = Role::where('name', 'Employee')->first();
        $staffRole->givePermissionTo($staffPermissions);

        $patientRole = Role::where('name', 'Patient')->first();
        $patientRole->givePermissionTo($patientPermissions);
    }
}
