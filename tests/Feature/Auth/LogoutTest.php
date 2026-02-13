<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs out authenticated user', function () {

    $user = User::factory()->create();
    $token = auth()->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/logout');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
});

