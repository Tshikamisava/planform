<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'display_name',
        'description',
        'level',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'level' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot(['assigned_by', 'assigned_at', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    public function activeUsers()
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->wherePivot('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->withPivot(['assigned_by', 'assigned_at', 'expires_at', 'is_active'])
            ->withTimestamps();
    }
}
