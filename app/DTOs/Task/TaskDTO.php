<?php

namespace App\DTOs\Task;

use App\DTOs\BaseDTO;

class TaskDTO extends BaseDTO
{
    public function __construct(
        public int $assigned_by,
        public int $assigned_to,
        public string $title,
        public ?string $description,
        public ?string $due_date,
        public string $status = 'pending'
    ) {}
    public function toArray():array{
        return [
            'assigned_by' => $this->assigned_by,
            'assigned_to' => $this->assigned_to,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'status' => $this->status,
        ];
    }
}
