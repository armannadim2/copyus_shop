<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PrintJob;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\Admin\NewTicketNotification;
use App\Notifications\TicketRepliedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->withCount('replies')
            ->latest()
            ->paginate(15);

        return view('shop.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $orders    = Order::where('user_id', Auth::id())->latest()->take(10)->get();
        $printJobs = PrintJob::where('user_id', Auth::id())
            ->whereNotIn('status', ['draft', 'in_cart', 'cancelled'])
            ->with('template')
            ->latest()
            ->take(10)
            ->get();

        return view('shop.tickets.create', compact('orders', 'printJobs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'      => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string', 'max:5000'],
            'order_id'     => ['nullable', 'integer', 'exists:orders,id'],
            'print_job_id' => ['nullable', 'integer', 'exists:print_jobs,id'],
        ]);

        // Validate ownership
        if ($request->order_id) {
            abort_unless(Order::where('id', $request->order_id)->where('user_id', Auth::id())->exists(), 403);
        }
        if ($request->print_job_id) {
            abort_unless(PrintJob::where('id', $request->print_job_id)->where('user_id', Auth::id())->exists(), 403);
        }

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'user_id'       => Auth::id(),
            'order_id'      => $request->order_id,
            'print_job_id'  => $request->print_job_id,
            'subject'       => $request->subject,
            'body'          => $request->body,
            'status'        => 'open',
            'priority'      => 'medium',
        ]);

        // Notify all admins
        User::where('role', 'admin')->each(
            fn($admin) => $admin->notify(new NewTicketNotification($ticket))
        );

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Tiquet ' . $ticket->ticket_number . ' creat. Et respondrem aviat.');
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 404);
        $ticket->load(['replies.user', 'order', 'printJob.template']);

        return view('shop.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 404);
        abort_if(in_array($ticket->status, ['resolved', 'closed']), 403, 'El tiquet ja està tancat.');

        $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $reply = TicketReply::create([
            'ticket_id'    => $ticket->id,
            'user_id'      => Auth::id(),
            'body'         => $request->body,
            'is_admin_reply' => false,
        ]);

        // Re-open if was in_progress
        if ($ticket->status === 'in_progress') {
            $ticket->update(['status' => 'open']);
        }

        // Notify admins
        User::where('role', 'admin')->each(
            fn($admin) => $admin->notify(new TicketRepliedNotification($ticket, $reply))
        );

        return back()->with('success', 'Resposta enviada.');
    }

    public function close(Ticket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 404);

        $ticket->update(['status' => 'closed', 'resolved_at' => now()]);

        return back()->with('success', 'Tiquet tancat.');
    }
}
