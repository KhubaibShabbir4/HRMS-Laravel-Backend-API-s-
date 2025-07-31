<?php

namespace App\Services;

use App\Models\Employee;
use App\DTOs\Employee\EmployeeDTO;

class EmployeeService
{
    public function all()
    {
        return Employee::latest()->get();
    }

    public function find(int $id)
    {
        return Employee::findOrFail($id);
    }

    public function create(EmployeeDTO $dto): Employee
    {
        return Employee::create([
            'name'        => $dto->name,
            'email'       => $dto->email,
            'designation' => $dto->designation,
        ]);
    }

    public function update(EmployeeDTO $dto, int $id): Employee
    {
        $employee = Employee::findOrFail($id);
        $employee->update([
            'name'        => $dto->name,
            'email'       => $dto->email,
            'designation' => $dto->designation,
        ]);
        return $employee;
    }

    public function delete(int $id): void
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
    }
}
