<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <x-dashboard.profile-card />
            <x-dashboard.medical-activities />
            <x-dashboard.wellness-data />
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <x-dashboard.promo-card />
            <x-dashboard.notifications />
            <x-dashboard.family-widget />
        </div>
    </div>
</x-app-layout>
