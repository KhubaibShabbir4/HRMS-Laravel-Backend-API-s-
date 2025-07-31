<?php

namespace App\Mail;

use App\Models\PerformanceReview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Contracts\Queue\ShouldQueue; // Uncomment if you want queued mail

class PerformanceReviewMail extends Mailable /* implements ShouldQueue */
{
    use Queueable, SerializesModels;

    public PerformanceReview $review;

    /**
     * @param  \App\Models\PerformanceReview  $review
     */
    public function __construct(PerformanceReview $review)
    {
        $this->review = $review;
    }


    public function build()
    {
        return $this->subject('Your Performance Review')
            ->view('emails.performance_review');  // or ->markdown('emails.performance_review') if using markdown components
    }
}
