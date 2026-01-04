<?php

namespace App\Models;

use App\Enums\Permission;
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChangeRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'dcr_id',
        'title',
        'description',
        'reason_for_change',
        'request_type',
        'priority',
        'due_date',
        'status',
        'impact',
        'impact_summary',
        'recommendations',
        'author_id',
        'recipient_id',
        'decision_maker_id',
        'attachments',
    ];

    protected $attributes = [
        'status' => 'Draft',
        'priority' => 'Medium',
        'request_type' => 'Standard',
    ];

    protected $casts = [
        'uuid' => 'string',
        'due_date' => 'date',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'id',
        'deleted_at',
    ];

    protected $appends = [
        'formatted_dcr_id',
        'status_color',
        'priority_color',
        'is_overdue',
        'days_until_due',
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function decisionMaker()
    {
        return $this->belongsTo(User::class, 'decision_maker_id');
    }

    public function impactAssessment()
    {
        return $this->hasOne(ImpactAssessment::class, 'change_request_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'change_request_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'change_request_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'resource_id')
            ->where('resource_type', 'change_request');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_resource_id')
            ->where('related_resource_type', 'change_request');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeByRecipient($query, $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Draft', 'Pending', 'In_Review', 'Approved', 'In_Progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Completed', 'Closed']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->startOfDay())
            ->whereNotIn('status', ['Completed', 'Closed', 'Cancelled']);
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now()->startOfDay())
            ->whereNotIn('status', ['Completed', 'Closed', 'Cancelled']);
    }

    // Accessors
    public function getFormattedDcrIdAttribute()
    {
        return 'DCR-' . $this->dcr_id;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Draft' => 'gray',
            'Pending' => 'yellow',
            'In_Review' => 'blue',
            'Approved' => 'green',
            'Rejected' => 'red',
            'In_Progress' => 'purple',
            'Completed' => 'emerald',
            'Closed' => 'slate',
            'Cancelled' => 'gray',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'Low' => 'green',
            'Medium' => 'yellow',
            'High' => 'orange',
            'Critical' => 'red',
            default => 'gray',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now()->startOfDay() && 
               !in_array($this->status, ['Completed', 'Closed', 'Cancelled']);
    }

    public function getDaysUntilDueAttribute()
    {
        return now()->startOfDay()->diffInDays($this->due_date, false);
    }

    // Methods
    public static function generateDcrId(): string
    {
        do {
            $dcrId = strtoupper(Str::random(8));
        } while (static::where('dcr_id', $dcrId)->exists());
        
        return $dcrId;
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'Draft';
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === 'Draft' && 
               $this->title && 
               $this->description && 
               $this->reason_for_change;
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['Pending', 'In_Review']);
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'Approved';
    }

    public function canBeClosed(): bool
    {
        return in_array($this->status, ['Completed', 'Rejected']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['Draft', 'Pending', 'In_Review']);
    }

    public function submit()
    {
        if (!$this->canBeSubmitted()) {
            throw new \Exception('DCR cannot be submitted');
        }

        $this->status = 'Pending';
        $this->save();

        // Create audit log
        $this->auditLogs()->create([
            'event_type' => 'DCR_SUBMITTED',
            'event_category' => 'Workflow',
            'action' => 'DCR submitted for review',
            'user_id' => auth()->id(),
            'resource_type' => 'change_request',
            'resource_id' => $this->id,
            'success' => true,
            'event_timestamp' => now(),
        ]);
    }

    public function approve($approverId, $reason = null)
    {
        if (!$this->canBeApproved()) {
            throw new \Exception('DCR cannot be approved');
        }

        $this->status = 'Approved';
        $this->save();

        // Create approval record
        $this->approvals()->create([
            'approver_id' => $approverId,
            'decision' => 'Approved',
            'decision_reason' => $reason,
            'approval_level' => 'Final',
            'sequence_order' => 1,
            'is_final' => true,
            'decided_at' => now(),
        ]);

        // Create audit log
        $this->auditLogs()->create([
            'event_type' => 'DCR_APPROVED',
            'event_category' => 'Workflow',
            'action' => 'DCR approved',
            'user_id' => $approverId,
            'resource_type' => 'change_request',
            'resource_id' => $this->id,
            'success' => true,
            'event_timestamp' => now(),
        ]);
    }

    public function reject($approverId, $reason)
    {
        if (!$this->canBeApproved()) {
            throw new \Exception('DCR cannot be rejected');
        }

        $this->status = 'Rejected';
        $this->save();

        // Create approval record
        $this->approvals()->create([
            'approver_id' => $approverId,
            'decision' => 'Rejected',
            'decision_reason' => $reason,
            'approval_level' => 'Final',
            'sequence_order' => 1,
            'is_final' => true,
            'decided_at' => now(),
        ]);

        // Create audit log
        $this->auditLogs()->create([
            'event_type' => 'DCR_REJECTED',
            'event_category' => 'Workflow',
            'action' => 'DCR rejected',
            'user_id' => $approverId,
            'resource_type' => 'change_request',
            'resource_id' => $this->id,
            'success' => true,
            'event_timestamp' => now(),
        ]);
    }

    public function complete($recipientId, $notes = null)
    {
        if (!$this->canBeCompleted()) {
            throw new \Exception('DCR cannot be completed');
        }

        $this->status = 'Completed';
        $this->save();

        // Create audit log
        $this->auditLogs()->create([
            'event_type' => 'DCR_COMPLETED',
            'event_category' => 'Workflow',
            'action' => 'DCR completed',
            'user_id' => $recipientId,
            'resource_type' => 'change_request',
            'resource_id' => $this->id,
            'success' => true,
            'event_timestamp' => now(),
        ]);
    }

    public function close($adminId, $reason = null)
    {
        if (!$this->canBeClosed()) {
            throw new \Exception('DCR cannot be closed');
        }

        $this->status = 'Closed';
        $this->save();

        // Create audit log
        $this->auditLogs()->create([
            'event_type' => 'DCR_CLOSED',
            'event_category' => 'Workflow',
            'action' => 'DCR closed',
            'user_id' => $adminId,
            'resource_type' => 'change_request',
            'resource_id' => $this->id,
            'success' => true,
            'event_timestamp' => now(),
        ]);
    }

    public function cancel($userId, $reason = null)
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('DCR cannot be cancelled');
        }

        $this->status = 'Cancelled';
        $this->save();

        // Create audit log
        $this->auditLogs()->create([
            'event_type' => 'DCR_CANCELLED',
            'event_category' => 'Workflow',
            'action' => 'DCR cancelled',
            'user_id' => $userId,
            'resource_type' => 'change_request',
            'resource_id' => $this->id,
            'success' => true,
            'event_timestamp' => now(),
        ]);
    }

    // Boot method for immutability
    protected static function booted()
    {
        static::updating(function ($changeRequest) {
            // Prevent status changes on closed/cancelled DCRs
            if (in_array($changeRequest->getOriginal('status'), ['Closed', 'Cancelled'])) {
                throw new \Exception('Cannot modify closed or cancelled DCR');
            }
        });
    }
}
