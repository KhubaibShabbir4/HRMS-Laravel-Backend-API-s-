<?php

namespace App\DTOs\PerformanceReview;

use App\DTOs\BaseDTO;

class PerformanceReviewDTO extends BaseDTO
{

    public function __construct(
        public int $user_id,
        public int $rating,
        public ?string $feedback,
        public string $review_date,
    ) {}
}
