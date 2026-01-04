<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized');
        }

        if (Auth::user()->isAdministrator()) {
            return $next($request);
        }

        // Redirect non-admin users
        return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
    }
}
