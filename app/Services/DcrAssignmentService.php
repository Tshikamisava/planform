<?php

namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DcrAssignmentService
{
    /**
     * Auto-assign a Decision Maker (DOM) to a DCR based on workload and impact.
     */
    public function autoAssignDecisionMaker(ChangeRequest $dcr): ?User
    {
        // Get all active DOMs
        $doms = User::whereHas('activeRoles', function ($query) {
            $query->where('name', 'dom');
        })->get();

        if ($doms->isEmpty()) {
            return null;
        }

        // For high-impact or critical priority, assign to least busy senior DOM
        if ($dcr->impact === 'High' || $dcr->priority === 'Critical') {
            return $this->assignToLeastBusyDom($doms, true);
        }

        // For standard cases, use round-robin with workload balancing
        return $this->assignToLeastBusyDom($doms, false);
    }

    /**
     * Find the DOM with the least active workload.
     */
    private function assignToLeastBusyDom($doms, bool $prioritizeSenior = false): User
    {
        $domWorkloads = [];

        foreach ($doms as $dom) {
            // Count active DCRs assigned to this DOM
            $activeCount = ChangeRequest::where('decision_maker_id', $dom->id)
                ->whereIn('status', ['Pending', 'In_Review'])
                ->count();

            // Count high-priority DCRs
            $highPriorityCount = ChangeRequest::where('decision_maker_id', $dom->id)
                ->whereIn('status', ['Pending', 'In_Review'])
                ->whereIn('priority', ['High', 'Critical'])
                ->count();

            // Calculate workload score (lower is better)
            $workloadScore = $activeCount + ($highPriorityCount * 2);

            $domWorkloads[] = [
                'dom' => $dom,
                'workload' => $workloadScore,
                'active_count' => $activeCount,
            ];
        }

        // Sort by workload (ascending)
        usort($domWorkloads, function ($a, $b) {
            return $a['workload'] <=> $b['workload'];
        });

        // Return the DOM with the least workload
        return $domWorkloads[0]['dom'];
    }

    /**
     * Determine if a DCR should be escalated based on impact and time.
     */
    public function shouldEscalateDcr(ChangeRequest $dcr): bool
    {
        // Escalate high-impact DCRs that are still pending after 2 days
        if ($dcr->impact === 'High' && $dcr->status === 'Pending') {
            $daysSinceCreation = now()->diffInDays($dcr->created_at);
            return $daysSinceCreation >= 2;
        }

        // Escalate critical priority DCRs that are still pending after 1 day
        if ($dcr->priority === 'Critical' && $dcr->status === 'Pending') {
            $daysSinceCreation = now()->diffInDays($dcr->created_at);
            return $daysSinceCreation >= 1;
        }

        // Escalate DCRs approaching due date with no progress
        if (in_array($dcr->status, ['Pending', 'In_Review'])) {
            $daysUntilDue = now()->diffInDays($dcr->due_date, false);
            return $daysUntilDue <= 2;
        }

        return false;
    }

    /**
     * Get administrators for escalation notifications.
     */
    public function getEscalationRecipients(): array
    {
        return User::whereHas('activeRoles', function ($query) {
            $query->whereIn('name', ['admin', 'dom']);
        })->get()->all();
    }
}
