<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;


uses(RefreshDatabase::class);

it('updates a task and clears cache for authorized user', function () {
    $role = $this->createRole('user');
    $permission = $this->createPermission('update tasks');
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole('user');

    $task = Task::factory()->create(['user_id' => $user->id]);

    // Pre-fill cache
    Cache::put("tasks_user_{$user->id}", ['dummy']);
    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
        ]);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
            ]
        ]);
});

it('returns 403 if unauthorized user tries to update', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $other = User::factory()->create();
    $token = auth('api')->login($other);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated',
            'description' => 'Updated',
            'status' => 'pending',
        ]);

    $response->assertStatus(403);
});

it('returns 404 if task is soft-deleted', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $task->delete();

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated',
            'description' => 'Updated',
            'status' => 'pending',
        ]);

    $response->assertStatus(404);
});

