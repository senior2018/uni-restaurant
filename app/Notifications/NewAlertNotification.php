<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewAlertNotification extends Notification implements ShouldQueue
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
            'type' => 'alert',
            'alert_id' => $this->alert->id,
            'order_id' => $this->alert->order_id,
            'user_id' => $this->alert->user_id,
            'reason' => $this->alert->reason,
            'created_at' => $this->alert->created_at,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'alert',
            'title' => 'New Alert',
            'message' => $this->alert->reason,
            'alert_id' => $this->alert->id,
            'order_id' => $this->alert->order_id,
            'user_id' => $this->alert->user_id,
            'url' => route('admin.alerts.index') . '#alert-' . $this->alert->id,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
