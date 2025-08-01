<?php

namespace App\Services\Task;

use App\DTOs\Task\TaskDTO;
use App\Filters\Task\TaskFilter;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskService
{

    public function create(array $data): Task
    {
        $dto = new TaskDTO(
            assigned_by: Auth::id(),
            assigned_to: $data['assigned_to'],
            title: $data['title'],
            description: $data['description'] ?? null,
            due_date: $data['due_date'] ?? null,
            status: 'pending'
        );

        return Task::create($dto->toArray());
    }

    public function updateWithData(Task $task, array $data): Task
    {
        $user = Auth::user();

        // Authorization
        if (! (
            $user->id === $task->assigned_by ||
            $user->hasRole('Admin') ||
            $user->id === $task->assigned_to
        )) {
            throw new AuthorizationException('Unauthorized to update this task.');
        }

        if ($user->id === $task->assigned_by || $user->hasRole('Admin')) {
            // Can update
            $dto = new TaskDTO(
                assigned_by: $task->assigned_by,
                assigned_to: $data['assigned_to'],
                title: $data['title'],
                description: $data['description'] ?? null,
                due_date: $data['due_date'] ?? null,
                status: $data['status']
            );
        } else {

            $dto = new TaskDTO(
                assigned_by: $task->assigned_by,
                assigned_to: $task->assigned_to,
                title: $task->title,
                description: $task->description,
                due_date: $task->due_date,
                status: $data['status']
            );
        }

        $task->update($dto->toArray());
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
    public function List(Request $request)
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

}
