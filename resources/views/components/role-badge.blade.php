{{-- Resources/views/components/role-badge.blade.php --}}
@php
@php

use App\Enums\Role;

@props(['role' => 'string', 'size' => 'sm'])

@php
$roleEnum = Role::tryFrom($role);
$colorClasses = match($size) {
    'xs' => 'text-xs',
    'sm' => 'text-sm',
    'md' => 'text-base',
    'lg' => 'text-lg',
    'xl' => 'text-xl',
};

$bgColorClasses = match($roleEnum) {
    Role::AUTHOR => 'bg-blue-100 text-blue-800',
    Role::RECIPIENT => 'bg-green-100 text-green-800',
    Role::DOM => 'bg-purple-100 text-purple-800',
    'Role::ADMIN => 'bg-red-100 text-red-800',
    default => 'bg-gray-100 text-gray-800',
};
?>

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full {{ $colorClasses[0] }} {{ $bgColorClasses[1] }}">
    {{ $roleEnum?->getDisplayName() ?? $role }}
</span>
