<?php

namespace App\Services;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    /**
     * Check if a user has a specific permission
     */
    public static function hasPermission(User $user, Permission $permission): bool
    {
        // Get user's active roles
        $activeRoles = $user->activeRoles()->pluck('name')->toArray();
        
        // Check each role for the permission
        foreach ($activeRoles as $roleName) {
            $role = Role::from($roleName);
            $rolePermissions = Permission::getForRole($role);
            
            if (in_array($permission, $rolePermissions)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if a user has any of the specified permissions
     */
    public static function hasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::hasPermission($user, $permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if a user has all of the specified permissions
     */
    public static function hasAllPermissions(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermission($user, $permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if a user has a specific role
     */
    public static function hasRole(User $user, Role $role): bool
    {
        return $user->hasRole($role->value);
    }

    /**
     * Check if a user has any of the specified roles
     */
    public static function hasAnyRole(User $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if (self::hasRole($user, $role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get all permissions for a user
     */
    public static function getUserPermissions(User $user): array
    {
        $cacheKey = "user_permissions_{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            $permissions = [];
            $activeRoles = $user->activeRoles()->pluck('name')->toArray();
            
            foreach ($activeRoles as $roleName) {
                $role = Role::from($roleName);
                $rolePermissions = Permission::getForRole($role);
                $permissions = array_merge($permissions, $rolePermissions);
            }
            
            return array_unique($permissions);
        });
    }

    /**
     * Get user's highest role level
     */
    public static function getUserHighestRole(User $user): ?Role
    {
        return $user->activeRoles()
            ->orderByDesc('level')
            ->first()
            ? Role::from($user->activeRoles()->orderByDesc('level')->first()->name)
            : null;
    }

    /**
     * Check if user can access admin functions
     */
    public static function canAccessAdmin(User $user): bool
    {
        return self::hasPermission($user, Permission::MANAGE_SYSTEM);
    }

    /**
     * Check if user can approve DCRs
     */
    public static function canApproveDcr(User $user): bool
    {
        return self::hasPermission($user, Permission::APPROVE_DCR);
    }

    /**
     * Check if user can create DCRs
     */
    public static function canCreateDcr(User $user): bool
    {
        return self::hasPermission($user, Permission::CREATE_DCR);
    }

    /**
     * Check if user can complete DCRs
     */
    public static function canCompleteDcr(User $user): bool
    {
        return self::hasPermission($user, Permission::COMPLETE_DCR);
    }

    /**
     * Check if user can access reports
     */
    public static function canAccessReports(User $user): bool
    {
        return self::hasPermission($user, Permission::ACCESS_REPORTS);
    }

    /**
     * Check if user can manage other users
     */
    public static function canManageUsers(User $user): bool
    {
        return self::hasPermission($user, Permission::MANAGE_USERS);
    }

    /**
     * Check if user can upload documents
     */
    public static function canUploadDocuments(User $user): bool
    {
        return self::hasPermission($user, Permission::UPLOAD_DOCUMENTS);
    }

    /**
     * Check if user can view audit logs
     */
    public static function canViewAuditLogs(User $user): bool
    {
        return self::hasPermission($user, Permission::VIEW_AUDIT_LOGS);
    }

    /**
     * Clear user permission cache
     */
    public static function clearUserPermissionCache(User $user): void
    {
        Cache::forget("user_permissions_{$user->id}");
    }

    /**
     * Log permission check for audit trail
     */
    public static function logPermissionCheck(User $user, Permission $permission, bool $granted): void
    {
        Log::info('Permission Check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'permission' => $permission->value,
            'granted' => $granted,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Validate user permissions for a specific action
     */
    public static function validateAction(User $user, string $action, $resource = null): bool
    {
        $permission = match($action) {
            'create_dcr' => Permission::CREATE_DCR,
            'approve_dcr' => Permission::APPROVE_DCR,
            'reject_dcr' => Permission::REJECT_DCR,
            'complete_dcr' => Permission::COMPLETE_DCR,
            'delete_dcr' => Permission::DELETE_DCR,
            'upload_documents' => Permission::UPLOAD_DOCUMENTS,
            'manage_users' => Permission::MANAGE_USERS,
            'access_reports' => Permission::ACCESS_REPORTS,
            default => null,
        };

        if (!$permission) {
            return false;
        }

        $hasPermission = self::hasPermission($user, $permission);
        self::logPermissionCheck($user, $permission, $hasPermission);
        
        return $hasPermission;
    }
}
