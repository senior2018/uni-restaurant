<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewOrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $customerName;

    public function __construct(Order $order, $customerName)
    {
        $this->order = $order;
        $this->customerName = $customerName;
    }

    public function via($notifiable)
    {
        return ['database']; // Add 'mail' if you want email too
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'new_order_placed',
            'order_id' => $this->order->id,
            'customer_name' => $this->customerName,
            'message' => "A new order #{$this->order->id} has been placed by {$this->customerName}."
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Order Placed - Our Restaurant')
            ->line("A new order #{$this->order->id} has been placed by {$this->customerName}.")
            ->line("Order total: $" . number_format($this->order->total_amount, 2))
            ->line("Please prepare the order for pickup/delivery.")
            ->action('View Order Details', url('/admin/orders/' . $this->order->id))
            ->line('Thank you for using Our Restaurant!');
    }
}
