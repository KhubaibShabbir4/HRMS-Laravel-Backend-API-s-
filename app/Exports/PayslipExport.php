<?php
// app/Exports/PayslipExport.php

namespace App\Exports;

use App\Models\Payroll;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayslipExport implements FromArray, WithHeadings
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function array(): array
    {
        $user = User::findOrFail($this->userId);
        $payroll = Payroll::where('user_id', $this->userId)->latest()->first();

        return [[
            'Name' => $user->name,
            'Email' => $user->email,
            'Month' => $payroll->pay_date ?? 'N/A',
            'Basic Salary' => $payroll->basic_pay ?? 0,
            'Allowances' => $payroll->bonuses ?? 0,
            'Deductions' => $payroll->deductions ?? 0,
            'Net Pay' => $payroll->net_salary ?? 0,
        ]];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Month',
            'Basic Salary',
            'Allowances',
            'Deductions',
            'Net Pay',
        ];
    }
}
