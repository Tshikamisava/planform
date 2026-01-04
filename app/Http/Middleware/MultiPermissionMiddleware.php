<?php

namespace App\Http\Middleware;

use App\Enums\Permission;
use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MultiPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
                'code' => 401
            ], 401);
        }

        // Convert string permissions to Permission enums
        $permissionEnums = [];
        foreach ($permissions as $permission) {
            try {
                $permissionEnums[] = Permission::from($permission);
            } catch (\ValueError $e) {
                return response()->json([
                    'message' => "Invalid permission specified: {$permission}",
                    'code' => 400
                ], 400);
            }
        }

        // Check if user has any of the required permissions
        $hasAnyPermission = false;
        foreach ($permissionEnums as $permissionEnum) {
            if (PermissionService::hasPermission($user, $permissionEnum)) {
                $hasAnyPermission = true;
                break;
            }
        }

        if (!$hasAnyPermission) {
            return response()->json([
                'message' => 'Insufficient permissions',
                'required_permissions' => $permissions,
                'code' => 403
            ], 403);
        }

        return $next($request);
    }
}
