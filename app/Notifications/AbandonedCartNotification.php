<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Collection $cartItems
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Has deixat coses a la cistella — Copyus')
            ->greeting('Hola, ' . $notifiable->name . '!')
            ->line('Has deixat ' . $this->cartItems->count() . ' article(s) a la cistella. Completa la teva comanda abans que s\'esgotin!');

        foreach ($this->cartItems->take(5) as $item) {
            $mail->line('• **' . $item->display_name . '** × ' . $item->quantity);
        }

        if ($this->cartItems->count() > 5) {
            $mail->line('… i ' . ($this->cartItems->count() - 5) . ' article(s) més.');
        }

        return $mail
            ->action('Completar la comanda', route('cart.index'))
            ->line('Si no volies fer cap comanda, pots ignorar aquest correu.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'abandoned_cart',
            'item_count' => $this->cartItems->count(),
        ];
    }
}
