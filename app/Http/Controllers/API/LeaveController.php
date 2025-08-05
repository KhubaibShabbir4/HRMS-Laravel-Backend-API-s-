<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\LeaveService;
use App\DTOs\Employee\LeaveRequestDTO;
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Mail\AcceptLeaveMail;
use Illuminate\Support\Facades\Mail;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function requestLeave(Request $request)
    {
        try {
            $dto = new LeaveRequestDTO($request->only(['start_date', 'end_date', 'reason']));
            $leave = $this->leaveService->requestLeave($dto);
            return response()->json(['message' => 'Leave requested successfully', 'data' => $leave]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
    public function approve($id)
    {
        $leave = $this->leaveService->approveLeave($id);
        return response()->json(['message' => 'Leave approved successfully.']);
    }

    public function reject($id)
    {
        $leave = $this->leaveService->rejectLeave($id);
        return response()->json(['message' => 'Leave rejected successfully.']);
    }



}

