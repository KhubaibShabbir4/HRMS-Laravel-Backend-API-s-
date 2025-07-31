<?php

namespace App\Filters\Task;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\Task\Status;
use App\Filters\Task\AssignedTo;
use App\Filters\Task\AssignedBy;
use App\Filters\Task\DueDate;

class TaskFilter
{
    protected Request $request;
    protected Builder $query;
    protected array $filters = [
        Status::class,
        AssignedTo::class,
        AssignedBy::class,
        DueDate::class,
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        $this->query = $query;

        foreach ($this->filters as $filterClass) {
            (new $filterClass)->handle($this->request, $this->query);
        }

        return $this->query;
    }
}
