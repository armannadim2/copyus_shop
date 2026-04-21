<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public string $reason = '') {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Actualització del teu compte Copyus')
            ->greeting('Hola, ' . $notifiable->name . '.')
            ->line('Lamentem informar-te que la teva sol·licitud de compte no ha estat aprovada.');

        if ($this->reason) {
            $mail->line('**Motiu:** ' . $this->reason);
        }

        return $mail
            ->line('Si creus que es tracta d\'un error, contacta\'ns a info@copyus.es.')
            ->action('Contactar-nos', url('/contact'));
    }
}
