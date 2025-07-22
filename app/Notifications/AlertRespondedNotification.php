<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AlertRespondedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $alert;

    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'alert_response',
            'alert_id' => $this->alert->id,
            'order_id' => $this->alert->order_id,
            'staff_id' => $this->alert->staff_id,
            'staff_response' => $this->alert->staff_response,
            'responded_at' => $this->alert->responded_at,
        ];
    }
}
