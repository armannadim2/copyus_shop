<?php

namespace App\Notifications\Admin;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewQuotationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Quotation $quotation) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New quote request: #' . $this->quotation->quote_number)
            ->greeting('New quote request!')
            ->line('A customer has submitted a new quote request.')
            ->line('**Quote:** #' . $this->quotation->quote_number)
            ->line('**Customer:** ' . $this->quotation->user->name . ' (' . ($this->quotation->user->company_name ?? $this->quotation->user->email) . ')')
            ->line('**Items:** ' . $this->quotation->items->count())
            ->action('Review quotation', route('admin.quotations.show', $this->quotation->quote_number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'new_quotation_submitted',
            'quotation_id' => $this->quotation->id,
            'quote_number' => $this->quotation->quote_number,
        ];
    }
}
