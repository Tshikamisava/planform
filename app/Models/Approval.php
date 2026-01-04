<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'change_request_id',
        'approver_id',
        'decision',
        'approval_level',
        'decision_reason',
        'conditions',
        'recommendations',
        'sequence_order',
        'is_final',
        'requires_next_approval',
        'decided_at',
        'effective_date',
        'expiry_date',
        'ip_address',
        'user_agent',
        'digital_signature',
    ];

    protected $casts = [
        'conditions' => 'array',
        'sequence_order' => 'integer',
        'is_final' => 'boolean',
        'requires_next_approval' => 'boolean',
        'decided_at' => 'datetime',
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(Dcr::class, 'change_request_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    public function isEffective()
    {
        return $this->effective_date && $this->effective_date <= now();
    }
}
