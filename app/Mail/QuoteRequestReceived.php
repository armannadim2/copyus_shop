<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteRequestReceived extends Mailable
{
    use SerializesModels;

    public function __construct(public QuoteRequest $quoteRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova sol·licitud de pressupost — ' . $this->quoteRequest->reference
                   . ' · ' . $this->quoteRequest->service_type,
            replyTo: [
                new Address($this->quoteRequest->email, $this->quoteRequest->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-request-received',
            with: ['quoteRequest' => $this->quoteRequest],
        );
    }
}
