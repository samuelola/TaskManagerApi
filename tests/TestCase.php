<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function createPermission($name, $guard = 'api') {
        return Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => $guard,
        ]);
    }

    protected function createRole($name, $guard = 'api')
    {
         return Role::firstOrCreate([
            'name' => $name,
            'guard_name' => $guard,
        ]);
    }



}
