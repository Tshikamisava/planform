<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                // Redirect authenticated users based on their role
                $redirectPath = $this->getRedirectPathForUser($user);
                
                return redirect($redirectPath);
            }
        }

        return $next($request);
    }

    /**
     * Get the redirect path for an authenticated user based on their role
     */
    protected function getRedirectPathForUser($user): string
    {
        // Admin users go to the main dashboard
        if ($user->isAdministrator()) {
            return route('dashboard', absolute: false);
        }

        // DOM users go to their approval dashboard
        if ($user->isDecisionMaker()) {
            return route('dcr.manager.dashboard', absolute: false);
        }

        // Recipient users go to their tasks
        if ($user->isRecipient()) {
            return route('dcr.my-tasks', absolute: false);
        }

        // Author users go to DCR dashboard
        if ($user->isAuthor()) {
            return route('dcr.dashboard', absolute: false);
        }

        // Default fallback to main dashboard
        return route('dashboard', absolute: false);
    }
}
