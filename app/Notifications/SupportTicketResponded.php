<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\SupportTicket;

class SupportTicketResponded extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        if (!$this->ticket->is_registered) {
            return ['mail'];
        }
        return ['database', 'mail'];
    }

    public function routeNotificationForMail($notifiable)
    {
        // Only override for unregistered users
        if (!$this->ticket->is_registered && $this->ticket->email) {
            return $this->ticket->email;
        }
        // Otherwise, let Laravel use the default (for registered users)
        return null;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Support Ticket Response: ' . $this->ticket->subject)
            ->markdown('emails.support-ticket-response', [
                'ticket' => $this->ticket,
                'name' => $this->ticket->name,
                'admin_response' => $this->ticket->admin_response,
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'support_ticket_response',
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'admin_response' => $this->ticket->admin_response,
            'status' => $this->ticket->status,
            'message' => 'Your support ticket has been responded to by admin.'
        ];
    }
}
