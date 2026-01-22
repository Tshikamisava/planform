<?php

namespace App\Models;

use App\Enums\Permission;
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ChangeRequest extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'change_requests';

    protected $fillable = [
        'uuid',
        'dcr_id',
        'title',
        'description',
        'reason_for_change',
        'request_type',
        'due_date',
        'status',
        'impact',
        'impact_summary',
        'recommendations',
        'author_id',
        'recipient_id',
        'decision_maker_id',
        'auto_escalated',
        'escalated_at',
        'attachments',
        // Impact analysis fields
        'cost',
        'weight',
        'tooling',
        'tooling_desc',
        'inventory_scrap',
        'parts',
        // Verification & Validation
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_notes',
        'is_validated',
        'validated_by',
        'validated_at',
        'validation_notes',
        // Closure
        'closure_status',
        'closed_by',
        'closed_at',
        'closure_notes',
        'closure_checklist',
        // Locking
        'is_locked',
        'locked_by',
        'locked_at',
        'lock_reason',
        // Archiving
        'is_archived',
        'archived_by',
        'archived_at',
        'archive_location',
        // Compliance
        'compliance_metadata',
        'last_modified_at',
        'last_modified_by',
    ];

    protected $attributes = [
        'status' => 'Draft',
        'request_type' => 'Standard',
    ];

    protected $casts = [
        'uuid' => 'string',
        'due_date' => 'date',
        'attachments' => 'array',
        'auto_escalated' => 'boolean',
        'escalated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Impact analysis casts
        'cost' => 'decimal:2',
        'weight' => 'decimal:3',
        'tooling' => 'boolean',
        'inventory_scrap' => 'boolean',
        'parts' => 'array',
        // Compliance casts
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
        'closure_checklist' => 'array',
        'compliance_metadata' => 'array',
        'last_modified_at' => 'datetime',
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

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function lastModifiedBy()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
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
            'uuid' => Str::uuid(),
            'event_type' => 'DCR_SUBMITTED',
            'event_category' => 'Workflow',
            'action' => 'DCR submitted for review',
            'user_id' => Auth::id(),
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

    // Boot method for immutability and compliance
    protected static function booted()
    {
        static::updating(function ($changeRequest) {
            // Enforce read-only on closed records
            if ($changeRequest->closure_status === 'Closed' && $changeRequest->isDirty()) {
                throw new \Exception('Cannot modify closed DCR - record is locked for compliance');
            }
            
            // Enforce read-only on locked records
            if ($changeRequest->is_locked && $changeRequest->isDirty(['title', 'description', 'reason_for_change'])) {
                throw new \Exception('Cannot modify locked DCR');
            }
            
            // Track last modification (commented out - columns don't exist in DB)
            // if ($changeRequest->isDirty() && !$changeRequest->is_locked) {
            //     $changeRequest->last_modified_at = now();
            //     $changeRequest->last_modified_by = Auth::id();
            // }
        });
    }

    // ==================== COMPLIANCE METHODS ====================
    
    /**
     * Verify the DCR
     */
    public function verify($userId, $notes = null)
    {
        if ($this->closure_status === 'Closed') {
            throw new \Exception('Cannot verify closed DCR');
        }

        $this->is_verified = true;
        $this->verified_by = $userId;
        $this->verified_at = now();
        $this->verification_notes = $notes;
        $this->save();

        $this->createAuditLog('DCR_VERIFIED', 'Verification', 'DCR verified', $userId);
        
        return $this;
    }

    /**
     * Validate the DCR
     */
    public function validate($userId, $notes = null)
    {
        if (!$this->is_verified) {
            throw new \Exception('DCR must be verified before validation');
        }
        
        if ($this->closure_status === 'Closed') {
            throw new \Exception('Cannot validate closed DCR');
        }

        $this->is_validated = true;
        $this->validated_by = $userId;
        $this->validated_at = now();
        $this->validation_notes = $notes;
        $this->save();

        $this->createAuditLog('DCR_VALIDATED', 'Validation', 'DCR validated', $userId);
        
        return $this;
    }

    /**
     * Close the DCR with full compliance process
     */
    public function closeDcr($userId, $notes = null, $checklist = [])
    {
        // Validate closure requirements
        if (!$this->canBeClosed()) {
            throw new \Exception('DCR does not meet closure requirements');
        }

        if (!$this->is_verified || !$this->is_validated) {
            throw new \Exception('DCR must be verified and validated before closure');
        }

        // Update closure fields
        $this->closure_status = 'Closed';
        $this->closed_by = $userId;
        $this->closed_at = now();
        $this->closure_notes = $notes;
        $this->closure_checklist = $checklist;
        $this->status = 'Closed';
        
        // Lock the record
        $this->lockRecord($userId, 'Closed for compliance');
        
        // Archive documents
        $this->archiveDocuments($userId);
        
        $this->save();

        $this->createAuditLog('DCR_CLOSED', 'Workflow', 'DCR closed', $userId);
        
        return $this;
    }

    /**
     * Lock the record
     */
    public function lockRecord($userId, $reason = 'Record locked')
    {
        if ($this->is_locked) {
            throw new \Exception('DCR is already locked');
        }

        $this->is_locked = true;
        $this->locked_by = $userId;
        $this->locked_at = now();
        $this->lock_reason = $reason;
        $this->save();

        $this->createAuditLog('DCR_LOCKED', 'Compliance', 'DCR locked', $userId);
        
        return $this;
    }

    /**
     * Unlock the record (admin only)
     */
    public function unlockRecord($userId, $reason = 'Record unlocked')
    {
        if (!$this->is_locked) {
            throw new \Exception('DCR is not locked');
        }
        
        if ($this->closure_status === 'Closed') {
            throw new \Exception('Cannot unlock closed DCR - compliance violation');
        }

        $this->is_locked = false;
        $this->locked_by = null;
        $this->locked_at = null;
        $this->lock_reason = null;
        $this->save();

        $this->createAuditLog('DCR_UNLOCKED', 'Compliance', $reason, $userId);
        
        return $this;
    }

    /**
     * Archive documents
     */
    public function archiveDocuments($userId)
    {
        if ($this->is_archived) {
            return $this;
        }

        $archivePath = "archives/dcr-{$this->dcr_id}/" . now()->format('Y-m-d');

        $this->is_archived = true;
        $this->archived_by = $userId;
        $this->archived_at = now();
        $this->archive_location = $archivePath;
        
        // Store compliance metadata
        $this->compliance_metadata = [
            'archived_document_count' => $this->documents()->count(),
            'archive_date' => now()->toDateTimeString(),
            'archived_by_user' => $userId,
            'verification_status' => $this->is_verified,
            'validation_status' => $this->is_validated,
            'closure_date' => $this->closed_at?->toDateTimeString(),
        ];
        
        $this->save();

        $this->createAuditLog('DOCUMENTS_ARCHIVED', 'Archival', 'Documents archived', $userId);
        
        return $this;
    }

    /**
     * Check if DCR can be modified
     */
    public function isReadOnly()
    {
        return $this->closure_status === 'Closed' || $this->is_locked || $this->is_archived;
    }

    /**
     * Check if DCR can be closed
     */
    public function canBeClosed()
    {
        return in_array($this->status, ['Completed', 'Approved']) 
               && $this->closure_status !== 'Closed';
    }

    /**
     * Create audit log helper
     */
    private function createAuditLog($eventType, $category, $action, $userId)
    {
        $this->auditLogs()->create([
            'event_type' => $eventType,
            'event_category' => $category,
            'action' => $action,
            'user_id' => $userId,
            'resource_type' => 'change_request',
            'resource_id' => $this->id,
            'success' => true,
            'event_timestamp' => now(),
        ]);
    }
}
