<?php

use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


//unit test dor TaskService
uses(TestCase::class, RefreshDatabase::class);

it('creates a task for user', function () {
    $user = User::factory()->create();
    $service = app(TaskService::class);

    $task = $service->store($user, [
        'title' => 'My Task'
    ]);

    expect($task->title)->toBe('My Task');
    expect($task->user_id)->toBe($user->id);
});

