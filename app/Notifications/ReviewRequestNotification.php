<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $this->order->locale ?? app()->getLocale();

        // Grab first product from the order for the mail subject
        $firstItem   = $this->order->items->first();
        $productName = $firstItem?->product_snapshot['name'] ?? '';

        $mail = (new MailMessage)
            ->subject(__('app.review_request_subject', ['order' => $this->order->order_number], $locale))
            ->greeting(__('app.welcome_back', [], $locale) . ', ' . $notifiable->name . '!')
            ->line(__('app.review_request_intro', ['order' => $this->order->order_number], $locale));

        // List each product in the order with a review link
        foreach ($this->order->items as $item) {
            if ($item->product && $item->product->is_active) {
                $mail->action(
                    __('app.review_product', ['name' => $item->product_snapshot['name'] ?? $item->product->sku], $locale),
                    route('products.show', $item->product->slug) . '#reviews'
                );
                break; // Only link to the first product — avoid email with 10 buttons
            }
        }

        $mail->line(__('app.review_request_thanks', [], $locale));

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'review_request',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }
}
