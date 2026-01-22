<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
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
        $cacheKey = "user_{$this->id}_has_role_{$roleName}";
        
        return \Cache::remember($cacheKey, 300, function () use ($roleName) {
            return $this->activeRoles()->where('name', $roleName)->exists();
        });
    }

    public function hasAnyRole(array $roleNames)
    {
        $cacheKey = "user_{$this->id}_has_any_role_" . md5(implode(',', $roleNames));
        
        return \Cache::remember($cacheKey, 300, function () use ($roleNames) {
            return $this->activeRoles()->whereIn('name', $roleNames)->exists();
        });
    }

    public function getHighestRoleLevel()
    {
        $cacheKey = "user_{$this->id}_highest_role_level";
        
        return \Cache::remember($cacheKey, 300, function () {
            return $this->activeRoles()->max('level') ?? 0;
        });
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

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission)
    {
        return \App\Services\PermissionService::hasPermission($this, $permission);
    }

    /**
     * Check if user has any of the specified permissions
     */
    public function hasAnyPermission(array $permissions)
    {
        return \App\Services\PermissionService::hasAnyPermission($this, $permissions);
    }

    /**
     * Check if user has all of the specified permissions
     */
    public function hasAllPermissions(array $permissions)
    {
        return \App\Services\PermissionService::hasAllPermissions($this, $permissions);
    }

    /**
     * Check if user can approve DCRs
     */
    public function canApproveDcr()
    {
        return $this->hasAnyRole(['dom', 'admin']);
    }

    /**
     * Check if user can reject DCRs
     */
    public function canRejectDcr()
    {
        return $this->hasAnyRole(['dom', 'admin']);
    }

    /**
     * Check if user can delete DCRs
     */
    public function canDeleteDcr()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUsers()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user can assign roles
     */
    public function canAssignRoles()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user can access system settings
     */
    public function canAccessSystem()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user can view reports
     */
    public function canAccessReports()
    {
        return $this->hasAnyRole(['dom', 'admin']);
    }

    /**
     * Check if user can view audit logs
     */
    public function canViewAuditLogs()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user can view a specific DCR
     */
    public function canViewDcr($dcr)
    {
        // Admin can view all
        if ($this->isAdministrator()) {
            return true;
        }

        // DOM can view all
        if ($this->isDecisionMaker()) {
            return true;
        }

        // Check if user is author, recipient, or decision maker for this DCR
        return $dcr->author_id === $this->id 
            || $dcr->recipient_id === $this->id 
            || $dcr->decision_maker_id === $this->id;
    }

    /**
     * Check if user can edit a specific DCR
     */
    public function canEditDcr($dcr)
    {
        // Admin can edit all
        if ($this->isAdministrator()) {
            return true;
        }

        // Author can edit own DCR if not yet approved
        return $dcr->author_id === $this->id && in_array($dcr->status, ['Draft', 'Pending Approval']);
    }

    /**
     * Clear cached role/permission data
     */
    public function clearRoleCache()
    {
        $patterns = [
            "user_{$this->id}_has_role_*",
            "user_{$this->id}_has_any_role_*",
            "user_{$this->id}_highest_role_level",
            "user_permissions_{$this->id}",
        ];

        foreach ($patterns as $pattern) {
            \Cache::forget($pattern);
        }
    }

    /**
     * Get the user's default home route based on their role
     */
    public function getHomeRoute(): string
    {
        // Admin users go to the main dashboard
        if ($this->isAdministrator()) {
            return route('dashboard');
        }

        // DOM users go to their approval dashboard
        if ($this->isDecisionMaker()) {
            return route('dcr.manager.dashboard');
        }

        // Recipient users go to their tasks
        if ($this->isRecipient()) {
            return route('dcr.my-tasks');
        }

        // Author users go to DCR dashboard
        if ($this->isAuthor()) {
            return route('dcr.dashboard');
        }

        // Default fallback to main dashboard
        return route('dashboard');
    }

    /**
     * Get the user's primary role (highest level)
     */
    public function getPrimaryRole(): ?string
    {
        if ($this->isAdministrator()) {
            return 'admin';
        }
        if ($this->isDecisionMaker()) {
            return 'dom';
        }
        if ($this->isRecipient()) {
            return 'recipient';
        }
        if ($this->isAuthor()) {
            return 'author';
        }

        return null;
    }
}
