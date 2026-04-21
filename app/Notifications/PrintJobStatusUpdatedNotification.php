<?php

namespace App\Notifications;

use App\Models\PrintJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrintJobStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly PrintJob $job,
        private readonly string   $previousStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale       = $notifiable->locale ?? app()->getLocale();
        $templateName = $this->job->template->getTranslation('name', $locale);

        [$subject, $headline, $body] = match ($this->job->status) {
            'in_production' => [
                '🖨️ El teu treball d\'impressió ha entrat en producció',
                'El teu treball ja s\'està produint',
                'Hem rebut el teu encàrrec de ' . $templateName . ' i ja ha entrat a la nostra línia de producció.',
            ],
            'completed' => [
                '✅ El teu treball d\'impressió està llest',
                'El teu treball és a punt!',
                'L\'encàrrec de ' . $templateName . ' ha estat completat i aviat el rebràs.',
            ],
            'cancelled' => [
                '❌ Treball d\'impressió cancel·lat',
                'El treball ha estat cancel·lat',
                'L\'encàrrec de ' . $templateName . ' ha estat cancel·lat. Contacta\'ns si tens alguna pregunta.',
            ],
            default => [
                'Actualització del teu treball d\'impressió',
                'Estat actualitzat',
                'El teu encàrrec de ' . $templateName . ' ha estat actualitzat.',
            ],
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hola, ' . $notifiable->name . '!')
            ->line($body)
            ->line('**Treball #' . $this->job->id . '** · ' . $templateName)
            ->line('Quantitat: **' . number_format($this->job->quantity, 0, ',', '.') . ' unitats**');

        if ($this->job->expected_delivery_at) {
            $mail->line('Data estimada de lliurament: **' . $this->job->expected_delivery_at->format('d/m/Y') . '**');
        }

        if ($this->job->status === 'in_production' && !$this->job->artwork_path) {
            $mail->line('⚠️ **Recorda:** Encara no hem rebut el teu arxiu de disseny. Puja\'l des de la teva comanda el més aviat possible per no retardar la producció.');
        }

        return $mail->action('Veure la meva comanda', route('orders.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'print_job_status_updated',
            'print_job_id'     => $this->job->id,
            'template_name'    => $this->job->template->getTranslation('name', 'ca'),
            'previous_status'  => $this->previousStatus,
            'new_status'       => $this->job->status,
            'quantity'         => $this->job->quantity,
        ];
    }
}
