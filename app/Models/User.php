<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'phone',
        'department',
        'position',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function dcrs()
    {
        return $this->hasMany(Dcr::class, 'author_id');
    }

    public function assignedDcrs()
    {
        return $this->hasMany(Dcr::class, 'recipient_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['assigned_by', 'assigned_at', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    public function activeRoles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->wherePivot('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->withPivot(['assigned_by', 'assigned_at', 'expires_at', 'is_active'])
            ->withTimestamps();
    }

    public function hasRole($roleName)
    {
        return $this->activeRoles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(array $roleNames)
    {
        return $this->activeRoles()->whereIn('name', $roleNames)->exists();
    }

    public function getHighestRoleLevel()
    {
        return $this->activeRoles()->max('level') ?? 0;
    }

    public function isAdministrator()
    {
        return $this->hasRole('admin');
    }

    public function isDecisionMaker()
    {
        return $this->hasRole('dom');
    }

    public function isRecipient()
    {
        return $this->hasRole('recipient');
    }

    public function isAuthor()
    {
        return $this->hasRole('author');
    }

    public function impactAssessments()
    {
        return $this->hasMany(ImpactAssessment::class, 'assessor_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('status', '!=', 'Read');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
