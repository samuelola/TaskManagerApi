<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Task Index Tests ie all task feature test

it('returns tasks for authenticated user', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->create(['user_id' => $user->id]);

    $token = auth()->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/tasks');

    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
});

