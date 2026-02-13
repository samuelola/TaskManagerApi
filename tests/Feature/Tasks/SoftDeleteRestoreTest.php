<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('restores a soft deleted task', function () {
    
    //  Create admin role 
    $role = $this->createRole('admin');
    $permission =  $this->createPermission('restore tasks');

    // assign permission to role
    $role->givePermissionTo($permission);

    //  Create admin user
    $user = User::factory()->create();
    $user->assignRole('admin');

    $token = auth()->login($user);

    // Create and soft delete task
    $task = Task::factory()->create(['user_id' => $user->id]);
    $task->delete();

    // restore endpoint
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson("/api/v1/tasks/{$task->id}/restore");

    $response->assertStatus(200);
    expect($task->fresh()->deleted_at)->toBeNull();
});
