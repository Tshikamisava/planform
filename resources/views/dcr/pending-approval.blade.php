<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pending Approval') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                    {{ $pendingDcrs->total() }} Pending
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">DCRs Awaiting Your Approval</h3>
                    
                    @if($pendingDcrs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-blue-600">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Request ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Author</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Request</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Priority</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Due Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingDcrs as $dcr)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $dcr->created_at->format('Y-m-d') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-blue-600">
                                                <a href="{{ route('dcr.show', $dcr) }}" class="hover:underline">
                                                    {{ $dcr->dcr_number ?? 'DCR-' . $dcr->id }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $dcr->author->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                <div class="max-w-xs truncate" title="{{ $dcr->reason }}">
                                                    {{ $dcr->reason }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @if($dcr->priority === 'High')
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">High</span>
                                                @elseif($dcr->priority === 'Medium')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Medium</span>
                                                @else
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Low</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                                    {{ $dcr->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $dcr->due_date ? $dcr->due_date->format('Y-m-d') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('dcr.show', $dcr) }}" 
                                                   class="text-blue-600 hover:text-blue-900 font-semibold">
                                                    Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $pendingDcrs->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending approvals</h3>
                            <p class="mt-1 text-sm text-gray-500">All DCRs have been reviewed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
