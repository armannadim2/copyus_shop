<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Notifications\TicketRepliedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTicketController extends Controller
{
    private const VALID_TRANSITIONS = [
        'open'        => ['in_progress', 'resolved', 'closed'],
        'in_progress' => ['resolved', 'closed'],
        'resolved'    => ['closed', 'open'],
        'closed'      => ['open'],
    ];

    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'order', 'printJob'])
            ->withCount('replies')
            ->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        $counts = Ticket::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.tickets.index', compact('tickets', 'counts'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['replies.user', 'user', 'order', 'printJob.template']);

        $allowedTransitions = self::VALID_TRANSITIONS[$ticket->status] ?? [];

        return view('admin.tickets.show', compact('ticket', 'allowedTransitions'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $reply = TicketReply::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => Auth::id(),
            'body'           => $request->body,
            'is_admin_reply' => true,
        ]);

        // Move to in_progress if still open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        // Notify customer
        $ticket->user?->notify(new TicketRepliedNotification($ticket, $reply));

        return back()->with('success', 'Resposta enviada al client.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $allowed = self::VALID_TRANSITIONS[$ticket->status] ?? [];

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', $allowed)],
        ]);

        $data = ['status' => $request->status];

        if (in_array($request->status, ['resolved', 'closed'])) {
            $data['resolved_at'] = now();
        } elseif ($request->status === 'open') {
            $data['resolved_at'] = null;
        }

        $ticket->update($data);

        return back()->with('success', 'Estat del tiquet actualitzat.');
    }
}
