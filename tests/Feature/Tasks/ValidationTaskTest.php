<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;


// Validation Tests ; purpose : test Request rules
uses(RefreshDatabase::class);


it('fails when required fields are missing', function () {
    $user = User::factory()->create();
    $token = auth()->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/tasks', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['title']);
});

