<?php

namespace App\Services\Payroll;


use App\DTOs\Payroll\CreatePayrollDTO;
use App\Models\Payroll;

class PayrollService
{
    public function create(CreatePayrollDTO $dto): Payroll
    {
        return Payroll::create($dto->toArray());
    }
}
