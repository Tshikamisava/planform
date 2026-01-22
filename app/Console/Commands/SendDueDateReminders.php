<?php

namespace App\Console\Commands;

use App\Models\ChangeRequest;
use App\Notifications\DcrDueDateReminderNotification;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dcr:send-due-date-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due date reminder notifications for DCRs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for DCRs with approaching due dates...');
        
        $now = now();
        $remindersSent = 0;
        
        // Find DCRs that are due within the next 3 days and are still active
        $dcrs = ChangeRequest::whereIn('status', ['Pending', 'In_Review', 'Approved', 'In_Progress'])
            ->where('due_date', '>=', $now)
            ->where('due_date', '<=', $now->copy()->addDays(3))
            ->with(['recipient', 'decisionMaker', 'author'])
            ->get();
        
        foreach ($dcrs as $dcr) {
            $daysRemaining = $now->diffInDays($dcr->due_date, false);
            
            // Only send reminders at specific intervals: 3 days, 1 day, and on due date
            if (!in_array($daysRemaining, [3, 1, 0])) {
                continue;
            }
            
            // Notify recipient if assigned and DCR is in their action stage
            if ($dcr->recipient_id && in_array($dcr->status, ['Approved', 'In_Progress'])) {
                $dcr->recipient->notify(new DcrDueDateReminderNotification($dcr, $daysRemaining));
                $remindersSent++;
                $this->line("Sent reminder to recipient for DCR {$dcr->dcr_id} (due in {$daysRemaining} days)");
            }
            
            // Notify decision maker if DCR is pending approval
            if ($dcr->decision_maker_id && in_array($dcr->status, ['Pending', 'In_Review'])) {
                $dcr->decisionMaker->notify(new DcrDueDateReminderNotification($dcr, $daysRemaining));
                $remindersSent++;
                $this->line("Sent reminder to DOM for DCR {$dcr->dcr_id} (due in {$daysRemaining} days)");
            }
            
            // Always notify the author
            $dcr->author->notify(new DcrDueDateReminderNotification($dcr, $daysRemaining));
            $remindersSent++;
            $this->line("Sent reminder to author for DCR {$dcr->dcr_id} (due in {$daysRemaining} days)");
        }
        
        $this->info("✓ Sent {$remindersSent} due date reminder(s) for {$dcrs->count()} DCR(s).");
        
        return Command::SUCCESS;
    }
}
