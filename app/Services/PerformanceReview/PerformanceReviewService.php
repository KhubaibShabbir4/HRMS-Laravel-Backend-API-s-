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

}
