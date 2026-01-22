<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:author,recipient,dom,viewer'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'email_verified_at' => now(), // Auto-verify for development
        ]);

        // Assign selected role to the user
        $role = \App\Models\Role::where('name', $request->role)->first();
        if ($role) {
            $user->roles()->attach($role->id, [
                'assigned_by' => null,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        $redirectUrl = $this->getRedirectUrlByRole($request->role);

        return redirect($redirectUrl)->with('success', 'Registration successful! Welcome to the DCR system.');
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrlByRole(string $role): string
    {
        return match($role) {
            'author' => route('dcr.create'),
            'recipient' => route('dcr.my-tasks'),
            'dom' => route('dcr.pending-approval'),
            'viewer' => route('dcr.dashboard'),
            default => route('dashboard'),
        };
    }
}
