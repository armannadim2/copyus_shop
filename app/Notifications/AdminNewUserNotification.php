<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewUserNotification extends Notification
{
    use Queueable;

    public function __construct(public User $newUser) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nou registre — ' . $this->newUser->company_name)
            ->greeting('Hola, Administrador!')
            ->line('Una nova empresa s\'ha registrat a Copyus i està esperant aprovació.')
            ->line('**Empresa:** ' . $this->newUser->company_name)
            ->line('**CIF:** ' . $this->newUser->cif)
            ->line('**Contacte:** ' . $this->newUser->name)
            ->line('**Email:** ' . $this->newUser->email)
            ->action('Revisar i Aprovar', url('/admin/users/' . $this->newUser->id))
            ->line('Accedeix al panell d\'administració per aprovar o rebutjar el compte.');
    }
}
