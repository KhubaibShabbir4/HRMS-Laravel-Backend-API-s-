<?php
namespace App\Services;

use App\Models\Leave;
use App\DTOs\Employee\LeaveRequestDTO;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveService
{
    public function requestLeave(LeaveRequestDTO $dto)
    {
        $user = Auth::user();
        $start = Carbon::parse($dto->start_date);
        $end = Carbon::parse($dto->end_date);
        $days = $start->diffInDays($end) + 1;

        if ($days > 10) {
            throw new \Exception("Leave cannot exceed 10 days.");
        }
        return Leave::create([
            'user_id' => $user->id,
            'start_date' => $dto->start_date,
            'end_date' => $dto->end_date,
            'reason' => $dto->reason,
            'status' => 'pending'
        ]);
    }
}

