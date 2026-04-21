<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Quotation $quotation) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $this->quotation->locale ?? app()->getLocale();

        return (new MailMessage)
            ->subject(__('app.quotation_submitted', [], $locale) . ' #' . $this->quotation->quote_number)
            ->greeting(__('app.welcome_back', [], $locale) . ', ' . $notifiable->name . '!')
            ->line(__('app.quotation_submitted', [], $locale))
            ->line(__('app.quote_number', [], $locale) . ': **#' . $this->quotation->quote_number . '**')
            ->line(__('app.quote_response_time', [], $locale))
            ->action(__('app.quotation', [], $locale), route('quotations.show', $this->quotation->quote_number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'quotation_submitted',
            'quotation_id' => $this->quotation->id,
            'quote_number' => $this->quotation->quote_number,
        ];
    }
}
