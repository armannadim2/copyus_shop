<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $this->order->locale ?? app()->getLocale();

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->invoice->load(['order.items']),
        ]);

        $mail = (new MailMessage)
            ->subject(__('app.order_placed_success', [], $locale) . ' #' . $this->order->order_number)
            ->greeting(__('app.welcome_back', [], $locale) . ', ' . $notifiable->name . '!')
            ->line(__('app.order_placed_success', [], $locale))
            ->line(__('app.order_number', [], $locale) . ': **#' . $this->order->order_number . '**')
            ->line(__('app.total', [], $locale) . ': **' . number_format($this->order->total, 2, ',', '.') . ' €**');

        // Artwork upload reminder for print job items
        $printItems = $this->order->items->filter(
            fn($item) => ($item->product_snapshot['type'] ?? null) === 'print_job'
        );

        if ($printItems->isNotEmpty()) {
            $mail->line('---')
                 ->line('🖨️ **La teva comanda inclou treballs d\'impressió.**')
                 ->line('Per poder iniciar la producció, recorda pujar l\'arxiu de disseny (PDF, AI, EPS o imatge d\'alta resolució) per a cada treball des del detall de la comanda.');
        }

        return $mail
            ->action(__('app.order_details', [], $locale), route('orders.show', $this->order->order_number))
            ->line(__('app.invoice_auto_generated', [], $locale))
            ->attachData($pdf->output(), 'invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'order_confirmed',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'total'        => $this->order->total,
        ];
    }
}
