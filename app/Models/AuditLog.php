<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'event_type',
        'event_category',
        'action',
        'user_id',
        'user_email',
        'session_id',
        'resource_type',
        'resource_id',
        'resource_uuid',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'user_agent',
        'request_method',
        'request_url',
        'hostname',
        'application_version',
        'success',
        'error_message',
        'event_timestamp',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'success' => 'boolean',
        'event_timestamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForResource($query, $resourceType, $resourceId)
    {
        return $query->where('resource_type', $resourceType)
                    ->where('resource_id', $resourceId);
    }

    public function scopeInCategory($query, $category)
    {
        return $query->where('event_category', $category);
    }

    public function scopeLastHours($query, $hours = 24)
    {
        return $query->where('event_timestamp', '>=', now()->subHours($hours));
    }

    // Prevent modification of audit logs (append-only)
    protected static function booted()
    {
        static::updating(function ($auditLog) {
            throw new \Exception('Audit logs cannot be modified');
        });

        static::deleting(function ($auditLog) {
            throw new \Exception('Audit logs cannot be deleted');
        });
    }
}
