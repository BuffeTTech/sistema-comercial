<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $roles = [];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // // Permissions
        // $permissionsWithRole = [
        //     'permission' => [
        //         'user',
        //         'commercial'
        //     ]
        // ];

        // foreach ($permissionsWithRole as $permission => $roles_permission) {
        //     $createdPermission = Permission::create(['name' => $permission]);

        //     foreach ($roles_permission as $roleName) {
        //         $role = Role::findByName($roleName);
        
        //         if ($role) {
        //             $role->givePermissionTo($createdPermission);
        //         }
        //     }
        // }
    }
}
