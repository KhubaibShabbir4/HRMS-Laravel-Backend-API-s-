<?php
namespace App\DTOs\Employee;

class LeaveRequestDTO
{
    public $start_date, $end_date, $reason;

    public function __construct($data)
    {
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
        $this->reason = $data['reason'] ?? null;
    }
}
