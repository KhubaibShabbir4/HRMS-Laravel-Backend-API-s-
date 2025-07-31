<?php

namespace App\DTOs\Payroll;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class CreatePayrollDTO extends BaseDTO
{
    public int $user_id;
    public float $basic_pay;
    public float $bonuses;
    public float $deductions;
    public string $pay_date;
    public float $net_salary;


    public function __construct(Request $request)
    {
        $this->user_id = (int) $request->input('user_id');
        $this->basic_pay = (float) $request->input('basic_pay');
        $this->bonuses = (float) $request->input('bonuses', 0);
        $this->deductions = (float) $request->input('deductions', 0);
        $this->net_salary = $this->basic_pay + $this->bonuses - $this->deductions;
        $this->pay_date = $request->input('pay_date');
    }
}
