<?php

namespace App\Notifications;

use App\Models\PrintJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArtworkUploadedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly PrintJob $job) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'artwork_uploaded',
            'print_job_id'  => $this->job->id,
            'template_name' => $this->job->template->getTranslation('name', 'ca'),
            'client_name'   => $this->job->user?->name,
            'company_name'  => $this->job->user?->company_name,
            'job_status'    => $this->job->status,
            'url'           => route('admin.print.jobs.show', $this->job->id),
        ];
    }
}
