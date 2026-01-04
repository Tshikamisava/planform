<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'type',
        'category',
        'title',
        'message',
        'user_id',
        'email',
        'role_id',
        'via_email',
        'via_in_app',
        'via_sms',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'failed_at',
        'related_resource_type',
        'related_resource_id',
        'action_url',
        'scheduled_at',
        'expires_at',
        'priority',
        'retry_count',
        'max_retries',
    ];

    protected $casts = [
        'via_email' => 'boolean',
        'via_in_app' => 'boolean',
        'via_sms' => 'boolean',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'failed_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'Sent');
    }
    
    public function scopeDelivered($query)
    {
        return $query->where('status', 'Delivered');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'Read');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '<=', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<', now());
    }

    public function markAsSent()
    {
        $this->update(['status' => 'Sent', 'sent_at' => now()]);
    }

    public function markAsDelivered()
    {
        $this->update(['status' => 'Delivered', 'delivered_at' => now()]);
    }

    public function markAsRead()
    {
        $this->update(['status' => 'Read', 'read_at' => now()]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->increment('retry_count');
        $this->update([
            'status' => 'Failed',
            'failed_at' => now(),
            'error_message' => $errorMessage
        ]);
    }

    public function canRetry()
    {
        return $this->retry_count < $this->max_retries;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }
}
