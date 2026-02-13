<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;


class TaskController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private TaskService $service) {}

   
    public function index(Request $request)
    {
        $tasks = $this->service->list(auth()->user(), $request->all());
        return TaskResource::collection($tasks);
    }

    
    public function store(StoreTaskRequest $request)
    {
        
        try {
            $this->authorize('create tasks');  // permission check
            $user = auth()->user();
            Cache::forget("tasks_user_{$user->id}");
            $task = $this->service->store($user, $request->validated());
            return new TaskResource($task);

        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'This action is unauthorized.'
            ], 403);
        }
    }

   
    public function show($id)
    {
        

        try {
            $task = Task::withTrashed()->find($id); // include soft-deleted

            if (!$task || $task->trashed()) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            $this->authorize('view', $task); // ownership policy
            $this->authorize('view tasks');  // permission check

            return new TaskResource($task);

        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'This action is unauthorized.'
            ], 403);
        }

        
    }


    
    public function update(UpdateTaskRequest $request, $id)
    {
        
        try {
           $task = Task::find($id);

            if (!$task || $task->trashed()) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            $this->authorize('update', $task); // ownership policy
            $this->authorize('update tasks');  // permission check
            $user = auth()->user();
            Cache::forget("tasks_user_{$user->id}");
            $task = $this->service->update($task, $request->validated());

            return new TaskResource($task);

        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'This action is unauthorized.'
            ], 403);
        }

        
    }


    
    public function destroy($id)
    {
        try {
            $task = Task::find($id);

            if (!$task || $task->trashed()) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            $this->authorize('delete', $task);   // policy (ownership)
            $this->authorize('delete tasks');    // permission

            $user = auth()->user();
            Cache::forget("tasks_user_{$user->id}");
            $task->delete();

            return response()->json(['message' => 'Task deleted'], 200);

        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'This action is unauthorized.'
            ], 403);
        }

    }


    
    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $this->authorize('restore', $task);  // ownership policy
        $this->authorize('restore tasks');  // permission check
        $task->restore();
        return new TaskResource($task);
    }
}
