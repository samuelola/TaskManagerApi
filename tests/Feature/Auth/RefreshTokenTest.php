<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);


it('refreshes jwt token', function () {

    $user = User::factory()->create();
    $token = auth()->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/refresh');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'token',
            'type',
            'expires_in',
            'role',
        ]);
});


