<?php

namespace App\Notifications\Admin;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New order placed: #' . $this->order->order_number)
            ->greeting('New order received!')
            ->line('A customer has placed a new order.')
            ->line('**Order:** #' . $this->order->order_number)
            ->line('**Customer:** ' . $this->order->user->name . ' (' . ($this->order->user->company_name ?? $this->order->user->email) . ')')
            ->line('**Total:** ' . number_format($this->order->total, 2, ',', '.') . ' €')
            ->action('View order', route('admin.orders.show', $this->order->order_number));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'new_order_placed',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'total'        => $this->order->total,
        ];
    }
}
