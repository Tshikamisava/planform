<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HighImpactDcrEscalationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ChangeRequest $dcr,
        public string $reason = 'High impact rating requires immediate attention'
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
            ->subject('🔴 HIGH IMPACT DCR - Immediate Attention Required: ' . $this->dcr->dcr_id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('⚠️ **ESCALATION ALERT**: A high-impact DCR requires your immediate attention.')
            ->line('**DCR ID:** ' . $this->dcr->dcr_id)
            ->line('**Title:** ' . $this->dcr->title)
            ->line('**Impact Rating:** ' . ($this->dcr->impact ?? 'High'))
            ->line('**Priority:** ' . $this->dcr->priority)
            ->line('**Request Type:** ' . $this->dcr->request_type)
            ->line('**Due Date:** ' . $this->dcr->due_date->toFormattedDateString())
            ->line('**Submitted By:** ' . $this->dcr->author->name)
            ->line('**Reason:** ' . $this->reason)
            ->action('Review Immediately', route('dcr.show', $this->dcr))
            ->line('This DCR has been escalated due to its high impact. Please review and take action promptly.');
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
            'type' => 'high_impact_escalation',
            'impact_rating' => $this->dcr->impact,
            'priority' => $this->dcr->priority,
            'due_date' => $this->dcr->due_date,
            'author_name' => $this->dcr->author->name,
            'reason' => $this->reason,
            'url' => route('dcr.show', $this->dcr),
        ];
    }
}
