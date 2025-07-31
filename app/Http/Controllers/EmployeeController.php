<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTOs\Employee\EmployeeDTO;
use App\Services\EmployeeService;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\UploadEmployeeFilesRequest;
use Illuminate\Support\Facades\Storage;


class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $employees = $this->employeeService->all();
        return EmployeeResource::collection($employees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:employees',
            'designation' => 'required|string|max:255',
        ]);

        $dto = new EmployeeDTO($validated);
        $employee = $this->employeeService->create($dto);

        return new EmployeeResource($employee);
    }

    public function show($id)
    {
        $employee = $this->employeeService->find($id);
        return new EmployeeResource($employee);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:employees,email,' . $id,
            'designation' => 'required|string|max:255',
        ]);

        $dto = new EmployeeDTO($validated);
        $employee = $this->employeeService->update($dto, $id);

        return new EmployeeResource($employee);
    }

    public function destroy($id)
    {
        $this->employeeService->delete($id);
        return response()->json(['message' => 'Employee deleted successfully']);
    }
    public function uploadFiles(UploadEmployeeFilesRequest $request, Employee $employee)
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

        return response()->json(['message' => 'Files uploaded successfully', 'employee' => $employee]);
    }


}
