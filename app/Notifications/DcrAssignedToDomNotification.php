<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DcrAssignedToDomNotification extends Notification implements ShouldQueue
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
            ->subject('DCR Requires Your Approval: ' . $this->dcr->dcr_id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A Document Change Request has been assigned to you for approval.')
            ->line('**DCR ID:** ' . $this->dcr->dcr_id)
            ->line('**Title:** ' . $this->dcr->title)
            ->line('**Request Type:** ' . $this->dcr->request_type)
            ->line('**Priority:** ' . $this->dcr->priority)
            ->line('**Impact Rating:** ' . ($this->dcr->impact ?? 'Not assessed'))
            ->line('**Due Date:** ' . $this->dcr->due_date->toFormattedDateString())
            ->line('**Submitted By:** ' . $this->dcr->author->name)
            ->action('Review & Approve', route('dcr.show', $this->dcr))
            ->line('Your timely review is appreciated.');
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
            'type' => 'dcr_assigned_to_dom',
            'priority' => $this->dcr->priority,
            'impact_rating' => $this->dcr->impact,
            'due_date' => $this->dcr->due_date,
            'author_name' => $this->dcr->author->name,
            'url' => route('dcr.show', $this->dcr),
        ];
    }
}
