<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'basic_pay',
        'bonuses',
        'deductions',
        'net_salary',
        'pay_date',
    ];

    // Relationship: Payroll belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function payslip()
    {
        return $this->hasOne(Payslip::class);
    }
}
