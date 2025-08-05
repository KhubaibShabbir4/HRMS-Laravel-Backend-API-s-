<?php

namespace App\Services\PerformanceReview;

use App\DTOs\PerformanceReview\PerformanceReviewDTO;
use App\Models\PerformanceReview;

class PerformanceReviewService
{

    public function create(PerformanceReviewDTO $dto): PerformanceReview
    {
        return PerformanceReview::create($dto->toArray());
    }

    public function update(PerformanceReview $review, PerformanceReviewDTO $dto): PerformanceReview
    {
        $review->update($dto->toArray());
        return $review;
    }

    public function delete(PerformanceReview $review): void
    {
        $review->delete();
    }

    public function calculateRatingFromTasks(int $userId): int
    {
        $totalTasks = \App\Models\Task::where('assigned_to', $userId)->count();
        $completedTasks = \App\Models\Task::where('assigned_to', $userId)
            ->where('status', 'completed')->count();

        if ($totalTasks === 0) return 1;

        $percentage = ($completedTasks / $totalTasks) * 100;

        return match (true) {
            $percentage >= 90 => 5,
            $percentage >= 75 => 4,
            $percentage >= 60 => 3,
            $percentage >= 40 => 2,
            default => 1,
        };
    }

    public function getReviewsForUser($user)
    {
        if ($user->hasRole('Admin')) {
            return \App\Models\PerformanceReview::with('user')->latest()->get();
        } elseif ($user->hasRole('HR')) {
            return \App\Models\PerformanceReview::with('user')
                ->whereHas('user', fn ($q) => $q->role(['Manager', 'Employee']))
                ->get();
        } elseif ($user->hasRole('Manager')) {
            return \App\Models\PerformanceReview::with('user')
                ->whereHas('user', fn ($q) => $q->role('Employee'))
                ->get();
        } else {
            return \App\Models\PerformanceReview::with('user')
                ->where('user_id', $user->id)
                ->get();
        }
    }

    public function findWithUser($id)
    {
        return \App\Models\PerformanceReview::with('user')->find($id);
    }

    public function find($id)
    {
        return \App\Models\PerformanceReview::find($id);
    }

    public function sendReminder($review_id)
    {
        $review = \App\Models\PerformanceReview::with('user')->findOrFail($review_id);
        $user = $review->user;
        \Mail::to($user->email)->send(new \App\Mail\PerformanceReviewMail($review));
        return $user->email;
    }
}
