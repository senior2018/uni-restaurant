<?php

namespace App\Notifications;

use App\Models\Rating;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewRatingNotification extends Notification implements ShouldQueue
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
            'type' => 'rating',
            'rating_id' => $this->rating->id,
            'order_id' => $this->rating->order_id,
            'meal_id' => $this->rating->meal_id,
            'user_id' => $this->rating->user_id,
            'rating' => $this->rating->rating,
            'comment' => $this->rating->comment,
            'created_at' => $this->rating->created_at,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'rating',
            'title' => 'New Rating',
            'message' => $this->rating->comment ?: 'You have a new rating.',
            'rating_id' => $this->rating->id,
            'order_id' => $this->rating->order_id,
            'meal_id' => $this->rating->meal_id,
            'user_id' => $this->rating->user_id,
            'rating' => $this->rating->rating,
            'url' => route('admin.ratings.index') . '#rating-' . $this->rating->id,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
