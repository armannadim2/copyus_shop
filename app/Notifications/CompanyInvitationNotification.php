<?php

namespace App\Notifications;

use App\Models\CompanyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompanyInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly CompanyInvitation $invitation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $company = $this->invitation->company;
        $roleLabel = match ($this->invitation->role) {
            'manager' => 'Gestor',
            'viewer'  => 'Visualitzador',
            default   => 'Comprador',
        };

        return (new MailMessage)
            ->subject('Invitació per unir-te a ' . $company->name . ' a Copyus')
            ->greeting('Hola!')
            ->line('Has estat convidat/da a unir-te a l\'empresa **' . $company->name . '** a la plataforma Copyus com a **' . $roleLabel . '**.')
            ->line('Aquesta invitació és vàlida durant 7 dies.')
            ->action('Acceptar invitació', route('company.invitation.show', $this->invitation->token))
            ->line('Si no esperaves aquesta invitació, pots ignorar aquest correu.');
    }
}
