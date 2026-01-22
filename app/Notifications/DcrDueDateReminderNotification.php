<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DcrDueDateReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ChangeRequest $dcr,
        public int $daysRemaining
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
        $urgency = $this->daysRemaining <= 1 ? 'URGENT' : 'REMINDER';
        $daysText = $this->daysRemaining === 1 ? 'tomorrow' : "in {$this->daysRemaining} days";
        
        return (new MailMessage)
            ->subject("{$urgency}: DCR Due {$daysText} - {$this->dcr->dcr_id}")
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("This is a reminder that the following DCR is due {$daysText}.")
            ->line('**DCR ID:** ' . $this->dcr->dcr_id)
            ->line('**Title:** ' . $this->dcr->title)
            ->line('**Priority:** ' . $this->dcr->priority)
            ->line('**Current Status:** ' . $this->dcr->status)
            ->line('**Due Date:** ' . $this->dcr->due_date->toFormattedDateString())
            ->action('View DCR', route('dcr.show', $this->dcr))
            ->line('Please take action to avoid delays.');
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
            'type' => 'due_date_reminder',
            'days_remaining' => $this->daysRemaining,
            'due_date' => $this->dcr->due_date,
            'status' => $this->dcr->status,
            'priority' => $this->dcr->priority,
            'url' => route('dcr.show', $this->dcr),
        ];
    }
}
