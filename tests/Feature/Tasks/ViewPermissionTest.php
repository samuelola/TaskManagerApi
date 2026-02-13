<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns a task for authorized user', function () {
    $role = $this->createRole('user');
    $permission = $this->createPermission('view tasks');
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole('user');

    $task = Task::factory()->create(['user_id' => $user->id]);

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/v1/tasks/{$task->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
            ]
        ]);
});

it('returns 403 if user does not have view permission', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $other = User::factory()->create();
    $token = auth('api')->login($other);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(403);
});

it('returns 404 if task is soft-deleted', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $task->delete();

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(404);
});
