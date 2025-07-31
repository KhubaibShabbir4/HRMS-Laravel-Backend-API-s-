<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'assigned_by' => [
                'id' => $this->assignedBy?->id,
                'name' => $this->assignedBy?->name,
                'email' => $this->assignedBy?->email,
            ],
            'assigned_to' => [
                'id' => $this->assignedTo?->id,
                'name' => $this->assignedTo?->name,
                'email' => $this->assignedTo?->email,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
