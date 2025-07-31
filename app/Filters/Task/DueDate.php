<?php

namespace App\Filters\Task;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DueDate
{
    public function handle(Request $request, Builder $query): void
    {
        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }
    }
}
