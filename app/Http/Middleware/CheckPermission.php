<?php

namespace App\Http\Middleware;

use App\Enums\Permission;
use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = $request->user();

        // Convert string permissions to Permission enums
        $requiredPermissions = array_map(
            fn($perm) => Permission::from($perm),
            $permissions
        );

        // Check if user has any of the specified permissions
        if (!PermissionService::hasAnyPermission($user, $requiredPermissions)) {
            abort(403, 'You do not have the required permissions to access this resource.');
        }

        return $next($request);
    }
}
