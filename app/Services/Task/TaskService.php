<?php

namespace App\Services\Task;

use App\DTOs\Task\TaskDTO;
use App\Models\Task;

class TaskService
{

    public function create(TaskDTO $dto): Task
    {
        return Task::create($dto->toArray());
    }

    public function update(Task $task, TaskDTO $dto): Task
    {
        $task->update($dto->toArray());
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

}
