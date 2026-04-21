<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Product $product) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Stock baix: ' . $this->product->getTranslation('name', 'ca'))
            ->line('El producte **' . $this->product->getTranslation('name', 'ca') . '** té stock baix.')
            ->line('SKU: ' . $this->product->sku)
            ->line('Stock actual: **' . $this->product->stock . '** unitats')
            ->line('Límit d\'alerta: ' . $this->product->low_stock_threshold . ' unitats')
            ->action('Veure producte', url(route('admin.products.edit', $this->product->id)))
            ->line('Actualitza l\'estoc des del panell d\'administració.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'low_stock',
            'product_id'  => $this->product->id,
            'product_name'=> $this->product->getTranslation('name', 'ca'),
            'sku'         => $this->product->sku,
            'stock'       => $this->product->stock,
            'threshold'   => $this->product->low_stock_threshold,
        ];
    }
}
