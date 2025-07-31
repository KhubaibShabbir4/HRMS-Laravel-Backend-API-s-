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
        $user = Auth::user();

        $query = Task::with(['assignedBy', 'assignedTo']);

        if ($user->hasRole('Admin')) {
            // Admin sees all tasks
        } elseif ($user->hasRole('HR') || $user->hasRole('Manager')) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        } else {
            $query->where('assigned_to', $user->id);
        }

        $query = (new TaskFilter($request))->apply($query);

        $tasks = $query->latest()->paginate(10);

        return TaskResource::collection($tasks)->additional([
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'total_pages' => $tasks->lastPage(),
            ]
        ]);
    }

    public function store(TaskRequest $request)
    {
        $dto = new TaskDTO(
            assigned_by: Auth::id(),
            assigned_to: $request->assigned_to,
            title: $request->title,
            description: $request->description,
            due_date: $request->due_date,
            status: 'pending'
        );

        $task = $this->taskService->create($dto);

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
        $user = Auth::user();

        if ($user->id === $task->assigned_by || $user->hasRole('Admin')) {
            $dto = new TaskDTO(
                assigned_by: $task->assigned_by,
                assigned_to: $request->assigned_to,
                title: $request->title,
                description: $request->description,
                due_date: $request->due_date,
                status: $request->status
            );
        } elseif ($user->id === $task->assigned_to) {
            $dto = new TaskDTO(
                assigned_by: $task->assigned_by,
                assigned_to: $task->assigned_to,
                title: $task->title,
                description: $task->description,
                due_date: $task->due_date,
                status: $request->status
            );
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $updated = $this->taskService->update($task, $dto);

        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => new TaskResource($updated)
        ]);
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

