<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs out from all devices', function () {

    $user = User::factory()->create();

    $token = auth('api')->login($user);

    $response1 = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/logout-all');

    $response1->assertOk();

    $response2 = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/logout-all');

    $response2->assertStatus(401); // token now invalid
});



