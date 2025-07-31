<?php

namespace App\Filters\Task;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AssignedBy
{
    public function handle(Request $request, Builder $query): void
    {
        if ($request->filled('assigned_by')) {
            $query->where('assigned_by', $request->assigned_by);
        }
    }
}
