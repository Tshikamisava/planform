<?php

namespace App\Http\Middleware;

use App\Enums\Permission;
use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
                'code' => 401
            ], 401);
        }

        try {
            $permissionEnum = Permission::from($permission);
        } catch (\ValueError $e) {
            return response()->json([
                'message' => 'Invalid permission specified',
                'code' => 400
            ], 400);
        }

        if (!PermissionService::hasPermission($user, $permissionEnum)) {
            return response()->json([
                'message' => 'Insufficient permissions',
                'required_permission' => $permission,
                'code' => 403
            ], 403);
        }

        return $next($request);
    }
}
