<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesAndPermissionsSeeder;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles & permissions for auth tests
    $this->seed(RolesAndPermissionsSeeder::class);
});


it('registers first user as admin', function () {

    $response = $this->postJson('/api/v1/register', [
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'password' => 'password',
        
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'token',
            'type',
            'expires_in',
            'role',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'admin@test.com',
    ]);

    $user = User::first();
    expect($user->hasRole('admin'))->toBeTrue();
});
