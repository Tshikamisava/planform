# DCR Notification System

## Overview

The DCR (Document Change Request) system includes a comprehensive notification framework built on Laravel's native notification system with queue support for optimal performance.

## Features Implemented

### 1. Recipient Notification on Submission ✅
- **Trigger**: When a DCR is created/submitted
- **Recipients**: Assigned recipient (implementer)
- **Notification Type**: `DcrSubmittedNotification`
- **Channels**: Email + Database
- **Content**: DCR ID, title, priority, due date, author info

### 2. Auto-Assignment of Decision Maker (DOM) ✅
- **Service**: `DcrAssignmentService`
- **Logic**: Workload-based distribution
  - Counts active DCRs per DOM
  - Weights high-priority DCRs (2x)
  - Assigns to least busy DOM
- **High-Impact**: Prioritizes senior DOMs for critical/high-impact DCRs
- **Notification**: `DcrAssignedToDomNotification` sent to assigned DOM

### 3. Due Date Reminder Notifications ✅
- **Command**: `dcr:send-due-date-reminders`
- **Schedule**: Daily at 8:00 AM
- **Intervals**: 3 days, 1 day, and on due date
- **Recipients**: 
  - Recipient (for Approved/In Progress DCRs)
  - Decision Maker (for Pending/In Review DCRs)
  - Author (always notified)
- **Notification Type**: `DcrDueDateReminderNotification`

### 4. High-Impact Escalation Notifications ✅
- **Trigger**: High impact rating or Critical priority
- **Command**: `dcr:check-escalations` (runs every 6 hours)
- **Escalation Criteria**:
  - High-impact DCR pending for 2+ days
  - Critical priority DCR pending for 1+ day
  - DCR within 2 days of due date without progress
- **Recipients**: All admins and DOMs
- **Notification Type**: `HighImpactDcrEscalationNotification`

## Notification Classes

All notifications implement `ShouldQueue` for asynchronous processing:

```php
DcrSubmittedNotification          // Sent to recipient
DcrAssignedToDomNotification      // Sent to Decision Maker
DcrDueDateReminderNotification    // Due date reminders
HighImpactDcrEscalationNotification // Escalation alerts
```

## Commands

### Send Due Date Reminders
```bash
php artisan dcr:send-due-date-reminders
```

### Check for Escalations
```bash
php artisan dcr:check-escalations
```

## Scheduled Tasks

Configured in `routes/console.php`:

```php
// Due date reminders - Daily at 8:00 AM
Schedule::command('dcr:send-due-date-reminders')
    ->dailyAt('08:00')
    ->timezone('Africa/Johannesburg');

// Escalation checks - Every 6 hours
Schedule::command('dcr:check-escalations')
    ->everySixHours()
    ->timezone('Africa/Johannesburg');
```

### Running the Scheduler

In production, add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

For local development:
```bash
php artisan schedule:work
```

## Queue Configuration

### Requirements
- Configure queue driver in `.env`: `QUEUE_CONNECTION=database`
- Run migrations: `php artisan queue:table && php artisan migrate`

### Running Queue Workers

Start a queue worker:
```bash
php artisan queue:work
```

For production with supervisor:
```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

## Database Schema

### Notifications Table
```php
- id (UUID)
- type (string)
- notifiable_type (string)
- notifiable_id (bigint)
- data (text/json)
- read_at (timestamp, nullable)
- timestamps
```

### Change Requests - Escalation Fields
```php
- auto_escalated (boolean, default: false)
- escalated_at (timestamp, nullable)
```

## Usage Examples

### Manual Notification Sending

```php
use App\Notifications\DcrSubmittedNotification;

$user->notify(new DcrSubmittedNotification($dcr));
```

### Check Unread Notifications

```php
$unreadNotifications = auth()->user()->unreadNotifications;

foreach ($unreadNotifications as $notification) {
    echo $notification->data['dcr_number'];
    echo $notification->data['title'];
}
```

### Mark as Read

```php
$notification->markAsRead();

// Or mark all as read
auth()->user()->unreadNotifications->markAsRead();
```

## Testing

### Test Due Date Reminders
```bash
php artisan dcr:send-due-date-reminders
```

### Test Escalations
```bash
php artisan dcr:check-escalations
```

### Test Queue Processing
```bash
php artisan queue:work --once
```

## Monitoring

### View Queued Jobs
```bash
php artisan queue:monitor
```

### Check Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
php artisan queue:retry all
```

## Email Configuration

Configure SMTP in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourapp.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Best Practices

1. **Always use queues** in production for email notifications
2. **Monitor queue workers** using supervisor or similar
3. **Set up failed job handling** and retry logic
4. **Test notifications** in staging before production
5. **Log notification failures** for troubleshooting
6. **Keep notification content concise** and actionable

## Troubleshooting

### Notifications not sending
- Check queue worker is running
- Verify email configuration
- Check `failed_jobs` table
- Review Laravel logs: `storage/logs/laravel.log`

### Escalations not triggering
- Verify scheduler is running
- Check escalation criteria in `DcrAssignmentService`
- Ensure `auto_escalated` field is properly tracked

### Performance issues
- Increase queue workers
- Use Redis for queue driver
- Optimize database queries with eager loading
