<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['view-operator', 'create-operator', 'update-operator', 'delete-operator'];

        $permissionsByRole = [
            'admin',
            'operator',
        ];

        foreach ($permissions as $value) {
            foreach ($permissionsByRole as $role) {
                Permission::firstOrCreate(['name' => $value], ['name' => $value]);
                $role = Role::firstOrCreate(['name' => $role], ['name' => $role]);
                $role->givePermissionTo($value);
            }
        }
    }
}
