<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class AdminContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::with('user')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20)->withQueryString();

        $counts = ContactMessage::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.contact_messages.index', compact('messages', 'counts'));
    }

    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->load('user');

        if ($contactMessage->status === 'new') {
            $contactMessage->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
        }

        return view('admin.contact_messages.show', compact('contactMessage'));
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:new,read,replied,archived'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $contactMessage->update($data);

        return back()->with('success', 'Missatge actualitzat.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Missatge eliminat.');
    }
}
