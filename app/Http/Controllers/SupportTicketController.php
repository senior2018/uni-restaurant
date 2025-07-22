<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Notifications\SupportTicketSubmitted;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SupportTicketResponded;
use Illuminate\Support\Facades\Log;

class SupportTicketController extends Controller
{
    use AuthorizesRequests;
    // Customer/guest submits a support ticket
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);
        $ticket = SupportTicket::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'user_id' => $request->user()?->id,
            'is_registered' => $request->input('is_registered', false),
        ]);
        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new SupportTicketSubmitted($ticket));
        return back()->with('success', 'Your message has been received. We will contact you soon.');
    }

    // Admin views all support tickets
    public function index(Request $request)
    {
        $this->authorize('viewAny', SupportTicket::class); // Policy recommended
        $tickets = SupportTicket::latest()->paginate(20);
        return Inertia::render('Admin/SupportTicketsIndex', [
            'tickets' => $tickets
        ]);
    }

    // Staff views their own support tickets
    public function myTickets(Request $request)
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->latest()
            ->get();
        return response()->json(['tickets' => $tickets]);
    }

    // Admin responds to a support ticket
    public function adminRespond(Request $request, SupportTicket $ticket)
    {
        $ticket->load('user');
        $this->authorize('view', $ticket); // Only admin/super_admin
        $validated = $request->validate([
            'admin_response' => 'required|string|max:2000',
            'status' => 'required|in:open,closed',
        ]);
        $ticket->update([
            'admin_response' => $validated['admin_response'],
            'status' => $validated['status'],
        ]);
        // Add detailed logging
        \Log::info('SupportTicket adminRespond: about to notify user', [
            'ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'is_registered' => $ticket->is_registered,
            'user_exists' => $ticket->user ? true : false,
            'user_email' => $ticket->user ? $ticket->user->email : null,
        ]);
        // Notify the staff user if exists (registered) or email unregistered
        if ($ticket->user_id && $ticket->user) {
            $ticket->user->notify(new \App\Notifications\SupportTicketResponded($ticket));
            \Log::info('SupportTicket adminRespond: notified user', [
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id,
            ]);
        } else if ($ticket->email) {
            \Notification::route('mail', $ticket->email)
                ->notify(new \App\Notifications\SupportTicketResponded($ticket));
            \Log::info('SupportTicket adminRespond: notified guest via email', [
                'ticket_id' => $ticket->id,
                'email' => $ticket->email,
            ]);
        }
        return response()->json(['success' => true, 'ticket' => $ticket]);
    }
}
