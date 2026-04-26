<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:160'],
            'phone'   => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create([
            'user_id' => Auth::id(),
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status'  => 'new',
        ]);

        try {
            Mail::to(config('mail.inbox'))
                ->send(new ContactMessageReceived($contactMessage));
        } catch (\Throwable $e) {
            Log::error('Failed to send contact-message email', [
                'id'        => $contactMessage->id,
                'inbox'     => config('mail.inbox'),
                'mailer'    => config('mail.default'),
                'host'      => config('mail.mailers.smtp.host'),
                'error'     => $e->getMessage(),
                'exception' => get_class($e),
            ]);
        }

        return redirect()->route('contact')
            ->with('success', 'Missatge enviat. Et respondrem aviat.');
    }
}
