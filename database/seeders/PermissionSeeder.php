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

        $permissions = [
            'view users',
            'view user logs',
            'edit admin approval',
            'create users',
            'edit users',
            'delete users',

            'view owner subusers',
            'invite owners',
            'view members',
            'invite members',
            
            'view subusers',
            'invite subusers',

            'view artists',
            'create artists',
            'edit artists',
            'delete artists',

            'view analytics',
            'create analytics',
            'edit analytics',
            'delete analytics',

            'view distributions',
            'create distributions',
            'edit distributions',
            'edit status distributions',
            'delete distributions',

            'view youtube services',
            'create youtube services',
            'edit youtube services',
            'delete youtube services',

            'view playlist services',
            'create playlist services',
            'edit playlist services',
            'delete playlist services',

            'view bank withdraws',
            'create bank withdraws',
            'edit bank withdraws',
            'delete bank withdraws',
            'update status bank withdraws',

            'view paypal withdraws',
            'create paypal withdraws',
            'edit paypal withdraws',
            'delete paypal withdraws',
            'update status paypal withdraws',

            'view legals',
            'create legals',
            'edit legals',
            'delete legals',

            'view announcements',
            'create announcements',
            'delete announcements',

            'view transactions',
            'create transactions',
            'debit transactions',

            'generate artworks',

            'view bank accounts',
            'create bank accounts',
            'edit bank accounts',
            'delete bank accounts',

            'view genres',
            'create genres',
            'edit genres',
            'delete genres',

            'view shops',
            'create shops',
            'edit shops',
            'delete shops',

            'view banks',
            'create banks',
            'edit banks',
            'delete banks',

            'view platforms',
            'create platforms',
            'edit platforms',
            'delete platforms',

            'view artwork templates',
            'create artwork templates',
            'edit artwork templates',
            'delete artwork templates',

            'view operators',
            'create operators',
            'edit operators',
            'delete operators',

            'view admins',
            'create admins',
            'edit admins',
            'delete admins',

            'manage permissions',
            'manage roles',
            'manage data master',
        ];

        $permissionsByRole = [
            'admin',
            'operator',
        ];

        foreach ($permissions as $value) {
            foreach ($permissionsByRole as $role) {
                Permission::firstOrCreate(['name' => $value], ['name' => $value]);
                // $role = Role::firstOrCreate(['name' => $role], ['name' => $role]);
                // $role->givePermissionTo($value);
            }
        }
    }
}
