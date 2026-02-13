<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TaskService
{
    public function list($user, $filters)
    {
        // without cache
        // return $user->tasks()
        //     ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
        //     ->latest()
        //     ->paginate(10);


        // with cache
        $key = "tasks_{$user->id}_" . md5(json_encode($filters));

        return Cache::remember($key, 60, function () use ($user, $filters) {
            return $user->tasks()
                ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
                ->latest()
                ->paginate(10);
        });
    }

    public function store($user, $data)
    {
        return $user->tasks()->create($data);
    }

    public function update($task, $data)
    {
        $task->update($data);
        return $task;
    }
}
