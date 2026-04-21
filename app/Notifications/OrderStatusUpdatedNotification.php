<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly string $previousStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $this->order->locale ?? app()->getLocale();
        $status = __('app.status_' . $this->order->status, [], $locale);

        return (new MailMessage)
            ->subject(__('app.order_status_updated_subject', [], $locale) . ' #' . $this->order->order_number)
            ->greeting(__('app.welcome_back', [], $locale) . ', ' . $notifiable->name . '!')
            ->line(__('app.order_status_updated_message', ['status' => $status], $locale))
            ->line(__('app.order_number', [], $locale) . ': **#' . $this->order->order_number . '**')
            ->line(__('app.order_status', [], $locale) . ': **' . $status . '**')
            ->action(__('app.order_details', [], $locale), route('orders.show', $this->order->order_number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'order_status_updated',
            'order_id'        => $this->order->id,
            'order_number'    => $this->order->order_number,
            'previous_status' => $this->previousStatus,
            'new_status'      => $this->order->status,
        ];
    }
}
