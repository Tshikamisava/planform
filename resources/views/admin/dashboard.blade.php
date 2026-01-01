<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Stat Card 1: Total Users -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold">Total Users</h3>
                                <p class="mt-2 text-3xl font-bold">{{-- USER_TOTAL --}}</p>
                            </div>
                        </div>

                        <!-- Stat Card 2: Total DCRs -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold">Total DCRs</h3>
                                <p class="mt-2 text-3xl font-bold">{{-- DCR_TOTAL --}}</p>
                            </div>
                        </div>

                        <!-- Stat Card 3: Pending Approvals -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold">Pending Approvals</h3>
                                <p class="mt-2 text-3xl font-bold">{{-- PENDING_APPROVALS --}}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Actions -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Admin Actions</h3>
                        <a href="{{-- route('admin.users.index') --}}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Manage Users</a>
                        <a href="{{ route('dcr.dashboard') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">View DCRs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
