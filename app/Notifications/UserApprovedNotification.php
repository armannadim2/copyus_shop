<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('El teu compte Copyus ha estat aprovat! 🎉')
            ->greeting('Benvingut/da, ' . $notifiable->name . '!')
            ->line('El teu compte a Copyus ha estat **aprovat**.')
            ->line('Ara pots accedir al catàleg complet amb preus, afegir productes a la cistella i sol·licitar pressupostos.')
            ->action('Accedir a la botiga', url('/dashboard'))
            ->line('Gràcies per confiar en Copyus!');
    }
}
