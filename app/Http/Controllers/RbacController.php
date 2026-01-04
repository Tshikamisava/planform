<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Models\Dcr;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RbacController extends Controller
{
    /**
     * Get current user's permissions
     */
    public function getUserPermissions(): JsonResponse
    {
        $user = Auth::user();
        $permissions = PermissionService::getUserPermissions($user);
        
        return response()->json([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'permissions' => $permissions,
            'highest_role' => PermissionService::getUserHighestRole($user)?->value,
            'can_access_admin' => PermissionService::canAccessAdmin($user),
            'can_approve_dcr' => PermissionService::canApproveDcr($user),
            'can_create_dcr' => PermissionService::canCreateDcr($user),
            'can_complete_dcr' => PermissionService::canCompleteDcr($user),
            'can_access_reports' => PermissionService::canAccessReports($user),
            'can_manage_users' => PermissionService::canManageUsers($user),
            'can_upload_documents' => PermissionService::canUploadDocuments($user),
            'can_view_audit_logs' => PermissionService::canViewAuditLogs($user),
        ]);
    }

    /**
     * Check if user has specific permission
     */
    public function checkPermission(Request $request, string $permission): JsonResponse
    {
        $user = Auth::user();
        
        try {
            $permissionEnum = Permission::from($permission);
            $hasPermission = PermissionService::hasPermission($user, $permissionEnum);
            
            return response()->json([
                'has_permission' => $hasPermission,
                'permission' => $permission,
                'user' => $user->email,
            ]);
        } catch (\ValueError $e) {
            return response()->json([
                'error' => 'Invalid permission specified',
                'available_permissions' => array_map(fn($p) => $p->value, Permission::cases()),
            ], 400);
        }
    }

    /**
     * Get role-based permission matrix
     */
    public function getPermissionMatrix(): JsonResponse
    {
        $matrix = [];
        
        foreach (\App\Enums\Role::cases() as $role) {
            $permissions = Permission::getForRole($role);
            $matrix[$role->value] = [
                'display_name' => $role->getDisplayName(),
                'level' => $role->getLevel(),
                'description' => $role->description(),
                'permissions' => array_map(fn($p) => $p->value, $permissions),
                'can_access_admin' => $role->canAccessAdmin(),
                'can_approve_dcr' => $role->canApproveDcr(),
                'can_create_dcr' => $role->canCreateDcr(),
                'can_complete_dcr' => $role->canCompleteDcr(),
                'can_access_reports' => $role->canAccessReports(),
            ];
        }
        
        return response()->json($matrix);
    }

    /**
     * Get all permissions grouped by category
     */
    public function getAllPermissions(): JsonResponse
    {
        return response()->json([
            'categories' => Permission::getAllGroupedByCategory(),
            'total_permissions' => count(Permission::cases()),
        ]);
    }

    /**
     * Test RBAC functionality
     */
    public function testRbac(Request $request): JsonResponse
    {
        $user = Auth::user();
        $testResults = [];

        // Test role-based checks
        $testResults['role_checks'] = [
            'is_author' => Gate::allows('is-author'),
            'is_recipient' => Gate::allows('is-recipient'),
            'is_dom' => Gate::allows('is-dom'),
            'is_admin' => Gate::allows('is-admin'),
        ];

        // Test permission-based checks
        $testResults['permission_checks'] = [
            'create_dcr' => Gate::allows('create-dcr'),
            'access_reports' => Gate::allows('access-reports'),
            'manage_users' => Gate::allows('manage-users'),
            'view_audit_logs' => Gate::allows('view-audit-logs'),
            'upload_documents' => Gate::allows('upload-documents'),
        ];

        // Test service-based checks
        $testResults['service_checks'] = [
            'can_access_admin' => PermissionService::canAccessAdmin($user),
            'can_approve_dcr' => PermissionService::canApproveDcr($user),
            'can_create_dcr' => PermissionService::canCreateDcr($user),
            'can_complete_dcr' => PermissionService::canCompleteDcr($user),
        ];

        return response()->json([
            'user' => $user->email,
            'test_results' => $testResults,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Clear permission cache for current user
     */
    public function clearPermissionCache(): JsonResponse
    {
        $user = Auth::user();
        PermissionService::clearUserPermissionCache($user);
        
        return response()->json([
            'message' => 'Permission cache cleared',
            'user' => $user->email,
        ]);
    }
}
