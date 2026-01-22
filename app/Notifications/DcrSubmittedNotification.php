<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DcrSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ChangeRequest $dcr
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New DCR Assigned: ' . $this->dcr->dcr_id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new Document Change Request has been assigned to you.')
            ->line('**DCR ID:** ' . $this->dcr->dcr_id)
            ->line('**Title:** ' . $this->dcr->title)
            ->line('**Request Type:** ' . $this->dcr->request_type)
            ->line('**Priority:** ' . $this->dcr->priority)
            ->line('**Due Date:** ' . $this->dcr->due_date->toFormattedDateString())
            ->line('**Submitted By:** ' . $this->dcr->author->name)
            ->action('View DCR', route('dcr.show', $this->dcr))
            ->line('Please review and take appropriate action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'dcr_id' => $this->dcr->id,
            'dcr_number' => $this->dcr->dcr_id,
            'title' => $this->dcr->title,
            'type' => 'dcr_submitted',
            'priority' => $this->dcr->priority,
            'due_date' => $this->dcr->due_date,
            'author_name' => $this->dcr->author->name,
            'url' => route('dcr.show', $this->dcr),
        ];
    }
}
