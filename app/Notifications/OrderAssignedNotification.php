<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $assignedBy; // user/admin who assigned

    public function __construct(Order $order, $assignedBy)
    {
        $this->order = $order;
        $this->assignedBy = $assignedBy;
    }

    public function via($notifiable)
    {
        return ['database']; // Add 'mail' if you want email too
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'order_assigned',
            'order_id' => $this->order->id,
            'assigned_by' => $this->assignedBy,
            'message' => "You have been assigned to order #{$this->order->id} by {$this->assignedBy}."
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Assigned to You')
            ->line("You have been assigned to order #{$this->order->id} by {$this->assignedBy}.")
            ->action('View Order', url('/staff/orders/' . $this->order->id));
    }
}
