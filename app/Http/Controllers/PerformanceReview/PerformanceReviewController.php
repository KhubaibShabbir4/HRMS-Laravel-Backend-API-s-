<?php

namespace App\Http\Controllers\PerformanceReview;

use App\DTOs\PerformanceReview\PerformanceReviewDTO;
use App\Http\Controllers\Controller;
use App\Mail\PerformanceReviewMail;
use App\Models\PerformanceReview;
use App\Models\User;
use App\Services\PerformanceReview\PerformanceReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PerformanceReviewController extends Controller
{
    protected PerformanceReviewService $reviewService;

    public function __construct(PerformanceReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index()
    {
        $user = Auth::user();
        $reviews = $this->reviewService->getReviewsForUser($user);
        return response()->json([
            'status' => true,
            'data' => $reviews,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'feedback'    => 'nullable|string',
            'review_date' => 'required|date',
        ]);

        $rating = $this->reviewService->calculateRatingFromTasks($request->user_id);

        $dto = new PerformanceReviewDTO(
            user_id: $request->user_id,
            rating: $rating,
            feedback: $request->feedback,
            review_date: $request->review_date,
        );

        $review = $this->reviewService->create($dto);

        return response()->json([
            'status' => true,
            'message' => 'Review created successfully.',
            'data' => $review,
        ]);
    }

    public function show($id)
    {
        $review = $this->reviewService->findWithUser($id);
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $review]);
    }

    public function update(Request $request, $id)
    {
        $review = $this->reviewService->find($id);
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review not found.'], 404);
        }
        $request->validate([
            'feedback'    => 'nullable|string',
            'review_date' => 'required|date',
        ]);
        $dto = new PerformanceReviewDTO(
            user_id: $review->user_id,
            rating: $this->reviewService->calculateRatingFromTasks($review->user_id),
            feedback: $request->feedback,
            review_date: $request->review_date,
        );
        $updated = $this->reviewService->update($review, $dto);
        return response()->json([
            'status' => true,
            'message' => 'Review updated successfully.',
            'data' => $updated,
        ]);
    }

    public function destroy($id)
    {
        $review = $this->reviewService->find($id);
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review not found.'], 404);
        }
        try {
            $this->reviewService->delete($review);
            return response()->json(['status' => true, 'message' => 'Review deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete the review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendReminders(Request $request)
    {
        $request->validate([
            'review_id' => 'required|exists:performance_reviews,id',
        ]);
        $email = $this->reviewService->sendReminder($request->review_id);
        return response()->json([
            'status' => true,
            'message' => 'Review reminder sent to ' . $email,
        ]);
    }
}
