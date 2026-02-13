<?php
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;


uses(RefreshDatabase::class);


it('fails to delete if user lacks permission', function () {
    // Role without delete permission
    $role = $this->createRole('user');
    // no permission assigned

    $user = User::factory()->create();
    $user->assignRole('user');

    $task = Task::factory()->create(['user_id' => $user->id]);

    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->deleteJson("/api/v1/tasks/{$task->id}");

    $response->assertStatus(403);
});


