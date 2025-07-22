<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCancellationDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $decision; // 'accepted' or 'rejected'
    public $byRole; // 'admin' or 'staff'
    public $byName;

    public function __construct(Order $order, $decision, $byRole, $byName = null)
    {
        $this->order = $order;
        $this->decision = $decision;
        $this->byRole = $byRole;
        $this->byName = $byName;
    }

    public function via($notifiable)
    {
        return ['database']; // Add 'mail' if you want email too
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'order_cancellation_decision',
            'order_id' => $this->order->id,
            'decision' => $this->decision,
            'by_role' => $this->byRole,
            'by_name' => $this->byName,
            'message' => "Order #{$this->order->id} cancellation request was {$this->decision} by {$this->byName}."
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Cancellation Request ' . ucfirst($this->decision))
            ->line("Your order #{$this->order->id} cancellation request was {$this->decision} by {$this->byName}.")
            ->action('View Order', url('/orders/' . $this->order->id));
    }
}
