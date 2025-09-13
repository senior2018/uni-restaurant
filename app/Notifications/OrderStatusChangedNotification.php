<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $oldStatus;
    public $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database']; // Add 'mail' if you want email too
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'order_status_changed',
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Your order #{$this->order->id} status changed from {$this->oldStatus} to {$this->newStatus}."
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('📦 Order Status Update - Our Restaurant')
            ->line("Hello! Your order #{$this->order->id} status has been updated.")
            ->line("Status changed from: {$this->oldStatus}")
            ->line("New status: {$this->newStatus}")
            ->line("We're working hard to get your delicious meal to you!")
            ->action('Track Your Order', url('/orders/' . $this->order->id))
            ->line('Thank you for choosing Our Restaurant!');
    }
}
