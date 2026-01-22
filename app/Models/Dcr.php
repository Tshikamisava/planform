<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dcr extends Model
{
    protected $fillable = [
        'dcr_id',
        'title',
        'description',
        'reason_for_change',
        'request_type',
        'due_date',
        'status',
        'impact',
        'impact_rating',
        'impact_summary',
        'recommendations',
        'author_id',
        'recipient_id',
        'decision_maker_id',
        'attachments',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $casts = [
        'due_date' => 'date',
        'attachments' => 'array',
    ];

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

    public function escalatedTo()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function impactAssessment()
    {
        return $this->hasOne(ImpactAssessment::class, 'change_request_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'change_request_id')
                    ->orderBy('sequence_order');
    }

    public function latestApproval()
    {
        return $this->hasOne(Approval::class, 'change_request_id')
                    ->latest('decided_at');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'change_request_id');
    }

    public function attachments()
    {
        return $this->documents()->where('document_type', 'Attachment');
    }

    public function supportingDocuments()
    {
        return $this->documents()->where('document_type', 'Supporting_Doc');
    }

    public function implementationDocuments()
    {
        return $this->documents()->where('document_type', 'Implementation_Doc');
    }

    public function closureDocuments()
    {
        return $this->documents()->where('document_type', 'Closure_Doc');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'resource');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'related_resource');
    }

    // Scopes for common queries
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['Closed', 'Cancelled']);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['Pending', 'In_Review']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'In_Progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['Closed', 'Cancelled', 'Completed']);
    }

    public function scopeEscalated($query)
    {
        return $query->whereNotNull('escalated_to');
    }

    public function scopeAutoEscalated($query)
    {
        return $query->where('auto_escalated', true);
    }

    public function scopeForAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeForRecipient($query, $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    public function scopeForDecisionMaker($query, $decisionMakerId)
    {
        return $query->where('decision_maker_id', $decisionMakerId);
    }

    // Helper methods
    public function isEditable()
    {
        return in_array($this->status, ['Draft']) && !$this->is_locked;
    }

    public function isSubmittable()
    {
        return $this->status === 'Draft' && !$this->is_locked;
    }

    public function isApprovable()
    {
        return in_array($this->status, ['Pending', 'In_Review']);
    }

    public function isImplementable()
    {
        return $this->status === 'Approved';
    }

    public function isCompletable()
    {
        return $this->status === 'In_Progress';
    }

    public function isClosable()
    {
        return $this->status === 'Completed';
    }

    public function isLocked()
    {
        return $this->is_locked || $this->status === 'Closed';
    }

    public function canTransitionTo($newStatus)
    {
        $validTransitions = [
            'Draft' => ['Pending', 'Cancelled'],
            'Pending' => ['In_Review', 'Rejected', 'Cancelled'],
            'In_Review' => ['Approved', 'Rejected', 'Pending', 'Cancelled'],
            'Approved' => ['In_Progress', 'Rejected', 'Cancelled'],
            'In_Progress' => ['Completed', 'Approved', 'Cancelled'],
            'Completed' => ['Closed', 'In_Progress'],
            'Rejected' => ['Pending', 'Cancelled'],
            'Closed' => [], // Final state
            'Cancelled' => ['Draft'], // Can be reopened
        ];

        return in_array($newStatus, $validTransitions[$this->status] ?? []);
    }

    public function getDaysUntilDueAttribute()
    {
        return now()->diffInDays($this->due_date, false);
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && !$this->isClosed();
    }

    public function lock(User $user = null)
    {
        $this->update([
            'is_locked' => true,
            'locked_by' => $user?->id,
            'locked_at' => now(),
        ]);
    }

    public function unlock()
    {
        $this->update([
            'is_locked' => false,
            'locked_by' => null,
            'locked_at' => null,
        ]);
    }

    // Prevent modification of closed DCRs
    protected static function booted()
    {
        static::updating(function ($dcr) {
            if ($dcr->status === 'Closed' && $dcr->isDirty(['status', 'is_locked'])) {
                throw new \Exception('Closed change requests cannot be modified');
            }
        });
    }
}
