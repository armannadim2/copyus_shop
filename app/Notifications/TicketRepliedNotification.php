<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Ticket      $ticket,
        private readonly TicketReply $reply
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $from = $this->reply->is_admin_reply ? 'Suport de Copyus' : $this->reply->user->name;

        return (new MailMessage)
            ->subject('[' . $this->ticket->ticket_number . '] Nova resposta: ' . $this->ticket->subject)
            ->greeting('Hola, ' . $notifiable->name . '!')
            ->line($from . ' ha respost al teu tiquet **' . $this->ticket->ticket_number . '**.')
            ->line('**' . $this->ticket->subject . '**')
            ->line(substr(strip_tags($this->reply->body), 0, 300) . (strlen($this->reply->body) > 300 ? '…' : ''))
            ->action('Veure tiquet', route('tickets.show', $this->ticket))
            ->line('Respon directament des de la plataforma.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'ticket_replied',
            'ticket_id'     => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject'       => $this->ticket->subject,
            'is_admin'      => $this->reply->is_admin_reply,
        ];
    }
}
