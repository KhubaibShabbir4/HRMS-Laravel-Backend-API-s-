<?php

namespace App\Http\Controllers\Task;

namespace App\Http\Controllers\Task;

use App\DTOs\Task\TaskDTO;
use App\Filters\Task\TaskFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\ErrorLoggingService;
use App\Services\Task\TaskService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    public function index(Request $request)
    {
       return $this->taskService->List($request);
    }

    public function store(TaskRequest $request)
    {
        $task = $this->taskService->create($request->validated());

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => new TaskResource($task)
        ], 201);
    }

    public function show(Task $task)
    {
        return response()->json(new TaskResource($task));
    }

    public function update(TaskRequest $request, Task $task)
    {
        try {
            $updated = $this->taskService->updateWithData($task, $request->validated());

            return response()->json([
                'message' => 'Task updated successfully.',
                'data' => new TaskResource($updated)
            ]);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();

        if ($user->id !== $task->assigned_by && !$user->hasRole('Admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->taskService->delete($task);

        return response()->json(['message' => 'Task deleted successfully.']);
    }

    public function markAsCompleted($id)
    {
        try {
            $task = Task::findOrFail($id);

            if (auth()->id() !== $task->assigned_to) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $task->status = 'completed';
            $task->save();

            return response()->json([
                'message' => 'Task marked as completed.',
                'data' => new TaskResource($task)
            ]);
        } catch (\Exception $e) {
            ErrorLoggingService::log($e);
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}

