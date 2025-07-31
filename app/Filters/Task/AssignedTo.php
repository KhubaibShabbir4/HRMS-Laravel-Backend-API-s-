<?php
namespace App\Filters\Task;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AssignedTo
{
    public function handle(Request $request, Builder $query): void
    {
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
    }
}
