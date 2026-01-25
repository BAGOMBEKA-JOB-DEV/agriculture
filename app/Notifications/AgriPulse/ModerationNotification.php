<?php

namespace App\Notifications\AgriPulse;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ModerationNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => $this->data['subject'],
            'message' => $this->data['message'],
            'priority' => $this->data['priority'] ?? 'normal',
            'action_url' => $this->data['action_url'] ?? null,
            'image_url' => $this->data['image_url'] ?? null,
            'content_details' => $this->data['content_details'] ?? [],
            'type' => 'agripulse_moderation'
        ];
    }
}
