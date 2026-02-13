<?php

use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;


// Cache Tests
uses(RefreshDatabase::class);

it('clears cache when creating task', function () {

    $permission = $this->createPermission('create tasks');

    Cache::spy();

    $user = User::factory()->create();
    $user->givePermissionTo($permission); // Give proper permission
    $token = auth()->login($user);

    $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/tasks', [
            'title' => 'Cached Task',
            'description' => 'Valid description',
        ])
        ->assertStatus(201);

    Cache::shouldHaveReceived('forget')
        ->with("tasks_user_{$user->id}");
});

