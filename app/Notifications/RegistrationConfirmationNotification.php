<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class RegistrationConfirmationNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? 'ca';
        $url    = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(trans('app.reg_email_subject', [], $locale))
            ->greeting(trans('app.reg_email_greeting', ['name' => $notifiable->name], $locale))
            ->line(trans('app.reg_email_thanks', [], $locale))
            ->line(trans('app.reg_email_pending', [], $locale))
            ->line(trans('app.reg_email_verify_instruction', [], $locale))
            ->action(trans('app.reg_email_verify_btn', [], $locale), $url)
            ->line(trans('app.reg_email_expire', ['minutes' => Config::get('auth.verification.expire', 60)], $locale))
            ->line(trans('app.reg_email_ignore', [], $locale));
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
