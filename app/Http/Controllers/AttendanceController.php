<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\DTOs\Attendance\CheckInDTO;
use App\DTOs\Attendance\CheckOutDTO;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function checkIn()
    {
        try {
            $user = auth()->user();

            $dto = new CheckInDTO([
                'user_id' => $user->id,
            ]);

            $attendance = $this->attendanceService->checkIn($dto);

            return response()->json(['data' => $attendance]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function checkOut()
    {
        try {
            $user = auth()->user();

            $dto = new CheckOutDTO([
                'user_id' => $user->id,
            ]);

            $attendance = $this->attendanceService->checkOut($dto);

            return response()->json(['data' => $attendance]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
