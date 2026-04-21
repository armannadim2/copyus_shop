<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationPricedNotification extends Notification implements ShouldQueue
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
            ->subject(__('app.quote_status_quoted', [], $locale) . ' #' . $this->quotation->quote_number)
            ->greeting(__('app.welcome_back', [], $locale) . ', ' . $notifiable->name . '!')
            ->line(__('app.quote_priced_message', [], $locale))
            ->line(__('app.quote_number', [], $locale) . ': **#' . $this->quotation->quote_number . '**')
            ->line(__('app.total', [], $locale) . ': **' . number_format($this->quotation->total_quoted, 2, ',', '.') . ' €**')
            ->when($this->quotation->valid_until, fn($msg) =>
                $msg->line(__('app.valid_until', [], $locale) . ': **' . $this->quotation->valid_until->format('d/m/Y') . '**')
            )
            ->action(__('app.accept_quote', [], $locale), route('quotations.show', $this->quotation->quote_number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'quotation_priced',
            'quotation_id'  => $this->quotation->id,
            'quote_number'  => $this->quotation->quote_number,
            'total_quoted'  => $this->quotation->total_quoted,
        ];
    }
}
