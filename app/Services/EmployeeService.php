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

    public function uploadFiles($request, Employee $employee)
    {
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $employee->resume_path = $resumePath;
        }

        if ($request->hasFile('contract')) {
            $contractPath = $request->file('contract')->store('contracts', 'public');
            $employee->contract_path = $contractPath;
        }

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
            $employee->document_path = $documentPath;
        }

        $employee->save();

        return $employee;
    }
}
