<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;

class ExportReady extends Notification
{
    use Queueable;

    protected $message;
    protected $url;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $message, ?string $url = null, string $type = 'success')
    {
        $this->message = $message;
        $this->url = $url;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => 'Governance Export Complete',
            'message' => $this->message,
            'action_url' => $this->url,
            'type' => $this->type,
            'priority' => 'normal',
            'action_needed' => (bool) $this->url
        ];
    }
}
