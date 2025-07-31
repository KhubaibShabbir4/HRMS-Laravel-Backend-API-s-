<?php

namespace App\Filters\Task;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class   Status
{
    public function handle(Request $request, Builder $query): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }
}
