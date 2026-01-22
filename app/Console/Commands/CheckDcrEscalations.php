<?php

namespace App\Console\Commands;

use App\Models\ChangeRequest;
use App\Services\DcrAssignmentService;
use App\Notifications\HighImpactDcrEscalationNotification;
use Illuminate\Console\Command;

class CheckDcrEscalations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dcr:check-escalations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for DCRs that need escalation and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for DCRs that require escalation...');
        
        $assignmentService = new DcrAssignmentService();
        $escalationsProcessed = 0;
        
        // Find active DCRs that might need escalation
        $dcrs = ChangeRequest::whereIn('status', ['Pending', 'In_Review'])
            ->where('auto_escalated', false)
            ->orWhereNull('auto_escalated')
            ->with(['author', 'recipient', 'decisionMaker'])
            ->get();
        
        foreach ($dcrs as $dcr) {
            if ($assignmentService->shouldEscalateDcr($dcr)) {
                // Determine escalation reason
                $reason = $this->getEscalationReason($dcr);
                
                // Get escalation recipients (admins and DOMs)
                $escalationRecipients = $assignmentService->getEscalationRecipients();
                
                // Send escalation notifications
                foreach ($escalationRecipients as $recipient) {
                    $recipient->notify(new HighImpactDcrEscalationNotification($dcr, $reason));
                }
                
                // Mark as escalated
                $dcr->update([
                    'auto_escalated' => true,
                    'escalated_at' => now(),
                ]);
                
                // Create audit log
                $dcr->auditLogs()->create([
                    'event_type' => 'DCR_ESCALATED',
                    'event_category' => 'Workflow',
                    'action' => 'DCR automatically escalated',
                    'user_id' => null,
                    'resource_type' => 'change_request',
                    'resource_id' => $dcr->id,
                    'success' => true,
                    'event_timestamp' => now(),
                    'metadata' => json_encode(['reason' => $reason]),
                ]);
                
                $escalationsProcessed++;
                $this->line("Escalated DCR {$dcr->dcr_id}: {$reason}");
            }
        }
        
        $this->info("✓ Processed {$escalationsProcessed} escalation(s).");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get the reason for escalation.
     */
    private function getEscalationReason(ChangeRequest $dcr): string
    {
        $daysSinceCreation = now()->diffInDays($dcr->created_at);
        $daysUntilDue = now()->diffInDays($dcr->due_date, false);
        
        if ($dcr->impact === 'High' && $daysSinceCreation >= 2) {
            return "High-impact DCR pending for {$daysSinceCreation} days without action";
        }
        
        if ($dcr->priority === 'Critical' && $daysSinceCreation >= 1) {
            return "Critical priority DCR pending for {$daysSinceCreation} days without action";
        }
        
        if ($daysUntilDue <= 2) {
            return "DCR approaching due date ({$daysUntilDue} days remaining) with no progress";
        }
        
        return "DCR requires immediate attention";
    }
}
