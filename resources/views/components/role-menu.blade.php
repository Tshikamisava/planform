{{-- Resources/views/components/role-menu.blade.php --}}
@php

@auth

@php
$user = auth()->user();
$activeRoles = $user->activeRoles;
$highestRole = \App\Services\PermissionService::getUserHighestRole($user);
?>

<div class="relative">
    <button class="flex items-center text-sm rounded-md px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <span class="mr-2">{{ $highestRole?->getDisplayName() }}</span>
        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1">
            @foreach ($activeRoles as $role)
                <div class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                    <div class="flex items-center">
                        <span class="w-2 h-2 rounded-full {{ \App\Enums\Role::from($role->name)->getDisplayName() === 'Administrator' ? 'bg-red-100' : \App\Enums\Role::from($role->name)->getDisplayName() === 'Decision Maker' ? 'bg-purple-100' : \App\Enums\Role::from($role->name)->getDisplayName() === 'Recipient' ? 'bg-green-100' : 'bg-blue-100' }} {{ \App\Enums\Role::from($role->name)->getDisplayName() === 'Author' ? 'text-blue-800' : \App\Enums\Role::from($role->name)->getDisplayName() === 'Decision Maker' ? 'text-purple-800' : \App\Enums\Role::from($role->name)->getDisplayName() === 'Recipient' ? 'text-green-800' : \App\Enums\Role::from($role->name)->getDisplayName() === 'Administrator' ? 'text-red-800' : 'text-blue-800' }}"></span>
                        <span class="ml-2">{{ \App\Enums\Role::from($role->name)->getDisplayName() }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
