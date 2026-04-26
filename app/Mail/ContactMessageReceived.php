<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable
{
    use SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nou missatge de contacte — ' . $this->contactMessage->subject,
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message-received',
            with: ['contactMessage' => $this->contactMessage],
        );
    }
}
