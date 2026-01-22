<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Update last login timestamp
        $request->user()->update(['last_login_at' => now()]);

        // Redirect based on user role
        $defaultRoute = $this->getDefaultRouteForUser($request->user());

        return redirect()->intended($defaultRoute);
    }

    /**
     * Get the default route for a user based on their role
     */
    protected function getDefaultRouteForUser($user): string
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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
