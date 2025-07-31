<?php
namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceService
{
    public function checkIn()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $userId)->where('date', $today)->first();
        if ($existing) {
            return ['message' => 'Already checked in'];
        }

        return Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => now()->format('H:i:s'),
        ]);
    }

    public function checkOut()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();

        if (!$attendance) {
            return ['message' => 'Not checked in yet'];
        }

        $attendance->update([
            'check_out' => now()->format('H:i:s')
        ]);

        return $attendance;
    }
}
