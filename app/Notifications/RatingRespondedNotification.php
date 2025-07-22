<?php

namespace App\Notifications;

use App\Models\Rating;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class RatingRespondedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $rating;

    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'rating_response',
            'rating_id' => $this->rating->id,
            'order_id' => $this->rating->order_id,
            'meal_id' => $this->rating->meal_id,
            'staff_id' => $this->rating->response_staff_id,
            'response_comment' => $this->rating->response_comment,
            'responded_at' => $this->rating->responded_at,
        ];
    }
}
