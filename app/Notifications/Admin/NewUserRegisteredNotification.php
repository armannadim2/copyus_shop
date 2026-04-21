<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly User $newUser) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New registration pending approval: ' . $this->newUser->name)
            ->greeting('New B2B registration!')
            ->line('A new user has registered and is pending approval.')
            ->line('**Name:** ' . $this->newUser->name)
            ->line('**Email:** ' . $this->newUser->email)
            ->line('**Company:** ' . ($this->newUser->company_name ?? '—'))
            ->line('**CIF:** ' . ($this->newUser->cif ?? '—'))
            ->action('Review user', route('admin.users.show', $this->newUser->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'new_user_registered',
            'user_id'      => $this->newUser->id,
            'user_name'    => $this->newUser->name,
            'company_name' => $this->newUser->company_name,
        ];
    }
}
