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

    public function canEditPayroll($currentUser, $user)
    {
        $role = $currentUser->getRoleNames()->first();
        if (($role === 'Manager' && $user->hasRole('Manager')) ||
            ($role === 'HR' && $user->hasRole('Admin')))
        {
            return false;
        }
        return true;
    }

    public function getPayrollForUser($user)
    {
        return $user->payroll ?? new \App\Models\Payroll();
    }

    public function updatePayroll($request, $user)
    {
        $dto = new \App\DTOs\Payroll\CreatePayrollDTO($request);
        $dto->user_id = $user->id;
        $payroll = $user->payroll ?? new \App\Models\Payroll(['user_id' => $user->id]);
        $payroll->fill($dto->toArray())->save();
        return $payroll;
    }

    public function canShowPayroll($currentUser, $user)
    {
        if (
            ($currentUser->hasPermissionTo('view all payrolls')) ||
            ($currentUser->hasPermissionTo('view hr and manager payrolls') &&
                ($user->hasRole('HR') || $user->hasRole('Manager')))
            || ($currentUser->hasPermissionTo('view employee payrolls') &&
                ($user->hasRole('HR') || $user->hasRole('Employee') || $user->hasRole('Manager')))
            || ($currentUser->hasPermissionTo('view own payroll') &&
                $currentUser->id === $user->id)
        ) {
            return true;
        }
        return false;
    }

    public function downloadPayslip($user_id)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PayslipExport($user_id), "Payslip_{$user_id}.xlsx");
    }
}
