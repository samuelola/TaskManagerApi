<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Always clear cache before seeding (Spatie requirement)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */
        $permissions = [
            'create tasks',
            'update tasks',
            'delete tasks',
            'view tasks',
            'restore tasks',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Roles
        |--------------------------------------------------------------------------
        */
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $user  = Role::firstOrCreate(['name' => 'user',  'guard_name' => 'api']);

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions to Roles
        |--------------------------------------------------------------------------
        */

        // Admin gets ALL permissions
        $admin->syncPermissions(Permission::all());

        // Regular user gets limited permissions
        $user->syncPermissions([
            'create tasks',
            'update tasks',
            'view tasks',
        ]);
    }
}
