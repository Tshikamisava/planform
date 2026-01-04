<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('DCR Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Quick Actions -->
            @if(auth()->user()->isAuthor() || auth()->user()->isAdministrator())
                <div class="mb-6">
                    <a href="{{ route('dcr.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Submit New DCR
                    </a>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 000 2v6a1 1 0 001 1h5a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1V2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total DCRs</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $submittedDcrs->count() + $assignedDcrs->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $submittedDcrs->where('status', 'Pending')->count() + $assignedDcrs->where('status', 'Pending')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-8-8a1 1 0 011.414-1.414L4 12.586V5a1 1 0 012 0v7.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $submittedDcrs->where('status', 'Approved')->count() + $assignedDcrs->where('status', 'Approved')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $submittedDcrs->where('status', 'Rejected')->count() + $assignedDcrs->where('status', 'Rejected')->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submitted DCRs -->
            @if($submittedDcrs->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Submitted DCRs</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($submittedDcrs as $dcr)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('dcr.show', $dcr) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $dcr->formatted_dcr_id }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($dcr->title, 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($dcr->status === 'Pending') bg-yellow-100 text-yellow-800
                                                @elseif($dcr->status === 'Approved') bg-green-100 text-green-800
                                                @elseif($dcr->status === 'Rejected') bg-red-100 text-red-800
                                                @elseif($dcr->status === 'Draft') bg-gray-100 text-gray-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ $dcr->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($dcr->priority === 'Critical') bg-red-100 text-red-800
                                                @elseif($dcr->priority === 'High') bg-orange-100 text-orange-800
                                                @elseif($dcr->priority === 'Medium') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ $dcr->priority }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $dcr->due_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('dcr.show', $dcr) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                            @if($dcr->canBeEdited())
                                                <a href="{{ route('dcr.edit', $dcr) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-1.5-3h6.75a1.5 1.5 0 001.5 1.5v6a1.5 1.5 0 001.5 1.5m-1.5-3V4.5m0 6a1.5 1.5 0 001.5 1.5v6a1.5 1.5 0 001.5-1.5m-1.5-3V4.5m0 6a1.5 1.5 0 001.5 1.5v6a1.5 1.5 0 001.5-1.5m-1.5-3V4.5m0 6a1.5 1.5 0 001.5-1.5v-6a1.5 1.5 0 011.5-1.5m-6 0a1.5 1.5 0 00-1.5 1.5v-6a1.5 1.5 0 011.5-1.5m-6 0a1.5 1.5 0 00-1.5 1.5v-6a1.5 1.5 0 011.5-1.5m-6 0a1.5 1.5 0 00-1.5 1.5v-6a1.5 1.5 0 011.5-1.5" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No submitted DCRs</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new DCR.</p>
                            <div class="mt-6">
                                <a href="{{ route('dcr.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Create DCR
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Assigned DCRs -->
            @if($assignedDcrs->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Assigned DCRs</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assignedDcrs as $dcr)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('dcr.show', $dcr) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $dcr->formatted_dcr_id }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($dcr->title, 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dcr->author->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($dcr->status === 'Pending') bg-yellow-100 text-yellow-800
                                                @elseif($dcr->status === 'Approved') bg-green-100 text-green-800
                                                @elseif($dcr->status === 'Rejected') bg-red-100 text-red-800
                                                @elseif($dcr->status === 'Draft') bg-gray-100 text-gray-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ $dcr->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $dcr->due_date->format('M d, Y') }}
                                            @if($dcr->is_overdue)
                                                <span class="ml-2 text-xs text-red-600">Overdue</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('dcr.show', $dcr) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                            @if($dcr->status === 'Approved')
                                                <button onclick="completeDcr({{ $dcr->id }})" class="text-green-600 hover:text-green-900">Complete</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2-2v-5m16 0v5a2 2 0 002 2H6a2 2 0 002 2v-5m16 0h-2M4 6h16"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No assigned DCRs</h3>
                            <p class="mt-1 text-sm text-gray-500">No DCRs have been assigned to you yet.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Performance optimizations
        document.addEventListener('DOMContentLoaded', function() {
            // Lazy load heavy content
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            });

            // Observe all tables
            document.querySelectorAll('table').forEach(table => {
                observer.observe(table);
            });

            // Debounce search/filter functionality
            let searchTimeout;
            function debounce(func, wait) {
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => func(...args), wait);
                    };
                    clearTimeout(searchTimeout);
                    later();
                };
            };

            // Handle DCR completion
            window.completeDcr = function(dcrId) {
                if (confirm('Are you sure you want to mark this DCR as completed?')) {
                    fetch(`/dcr/${dcrId}/complete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while completing the DCR.');
                    });
                }
            };

            // Auto-refresh dashboard every 30 seconds (only if page is visible)
            let refreshInterval;
            function startAutoRefresh() {
                refreshInterval = setInterval(() => {
                    if (!document.hidden) {
                        location.reload();
                    }
                }, 30000);
            }

            function stopAutoRefresh() {
                clearInterval(refreshInterval);
            }

            // Start auto-refresh
            startAutoRefresh();

            // Stop auto-refresh when page is hidden
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoRefresh();
                } else {
                    startAutoRefresh();
                }
            });

            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                stopAutoRefresh();
            });
        });
    </script>
</x-app-layout>
