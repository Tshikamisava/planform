{{-- Resources/views/components/permission-guard.blade.php --}}
@php
@php

@auth

@php
$permissions = $permissions ?? [];
$user = auth()->user();

// Check if user has any of the required permissions
$hasPermission = false;
foreach ($permissions as $permission) {
    try {
        $permissionEnum = \App\Enums\Permission::from($permission);
        if (\App\Services\PermissionService::hasPermission($user, $permissionEnum)) {
            $hasPermission = true;
            break;
        }
    } catch (\ValueError $e) {
        // Invalid permission
        $hasPermission = false;
    }
}

@if (!$hasPermission)
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 00-16 8 8 0 00-16 0zm-1 1a7 7 0 1113.936 13.936 14.071 1.071 1.071 1.071 1.071H18a1 1 0 001-1h-4a1 1 0 00-1h-4a1 1 0 00-1zM3 10a1 1 0 011-1h4a1 1 0 011 1H4a1 1 0 011-1h4a1 1 0 011-1z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Access Denied</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>You don't have permission to access this resource.</p>
                    @if (!empty($permissions))
                        <p class="mt-1">Required permissions: {{ implode(', ', $permissions) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    {{ $slot }}
@endif
