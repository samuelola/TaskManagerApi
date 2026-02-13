<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;


// Authorization and permission  Test

uses(RefreshDatabase::class);

it('forbids user without permission', function () {
    $user = User::factory()->create(); // no permission
    $token = auth()->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/tasks', [
            'title' => 'Test Task',
            'description' => 'Some description'
        ]);

    $response->assertStatus(403);
});
