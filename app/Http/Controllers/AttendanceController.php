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
            $attendance = $this->attendanceService->checkIn();
            return response()->json(['data' => $attendance]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function checkOut()
    {
        try {
            $attendance = $this->attendanceService->checkOut();
            return response()->json(['data' => $attendance]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
