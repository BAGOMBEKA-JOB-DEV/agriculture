<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Support\SupportTicket;

class SupportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $type;
    protected $data;

    /**
     * Create a new notification instance.
     * 
     * @param SupportTicket $ticket
     * @param string $type (ticket_created, ticket_replied, ticket_assigned, sla_warning, ticket_closed)
     * @param array $data Additional context
     */
    public function __construct(SupportTicket $ticket, string $type, array $data = [])
    {
        $this->ticket = $ticket;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $message = $this->getMessage();

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello, {$notifiable->name}")
            ->line($message)
            ->action('View Ticket Details', url("/support/tickets/{$this->ticket->id}"))
            ->line('Thank you for using our support system!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'type' => $this->type,
            'notification_title' => $this->getSubject(),
            'notification_message' => $this->getMessage(),
            'link' => "/support/tickets/{$this->ticket->id}"
        ];
    }

    protected function getSubject(): string
    {
        switch ($this->type) {
            case 'ticket_created':
                return "Ticket Created: #{$this->ticket->ticket_number}";
            case 'ticket_replied':
                return "New Reply on Ticket: #{$this->ticket->ticket_number}";
            case 'ticket_assigned':
                return "Ticket Assigned: #{$this->ticket->ticket_number}";
            case 'sla_warning':
                return "SLA Warning: Ticket #{$this->ticket->ticket_number} is at risk";
            case 'ticket_closed':
                return "Ticket Closed: #{$this->ticket->ticket_number}";
            default:
                return "Support Update: #{$this->ticket->ticket_number}";
        }
    }

    protected function getMessage(): string
    {
        switch ($this->type) {
            case 'ticket_created':
                return "Your support ticket regarding '{$this->ticket->subject}' has been successfully created.";
            case 'ticket_replied':
                return "A new message has been added to your ticket: '{$this->ticket->subject}'.";
            case 'ticket_assigned':
                return "Your ticket has been assigned to an agent and is being reviewed.";
            case 'sla_warning':
                return "The ticket '{$this->ticket->subject}' is approaching its SLA response limit.";
            case 'ticket_closed':
                return "The ticket regarding '{$this->ticket->subject}' has been marked as closed.";
            default:
                return "There is a new update on your support ticket.";
        }
    }
}
