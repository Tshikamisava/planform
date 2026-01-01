<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approval Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $pendingDcrs->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">High Impact</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $highImpactDcrs->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Approved Today</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $approvedToday->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Escalated</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $escalatedDcrs->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- High Priority DCRs -->
            @if($highImpactDcrs->count() > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">High Priority DCRs Require Immediate Attention</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ $highImpactDcrs->count() }} high impact DCR(s) are pending review and may require escalation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Pending DCRs for Approval -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Pending DCRs for Approval</h3>
                        
                        <!-- Filter Tabs -->
                        <div class="flex space-x-1 bg-gray-100 rounded-lg p-1">
                            <button onclick="filterDcrs('all')" 
                                    class="filter-btn px-3 py-1 text-sm font-medium rounded-md bg-white text-gray-700 shadow-sm"
                                    data-filter="all">
                                All ({{ $pendingDcrs->count() }})
                            </button>
                            <button onclick="filterDcrs('high')" 
                                    class="filter-btn px-3 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700"
                                    data-filter="high">
                                High ({{ $highImpactDcrs->count() }})
                            </button>
                            <button onclick="filterDcrs('escalated')" 
                                    class="filter-btn px-3 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700"
                                    data-filter="escalated">
                                Escalated ({{ $escalatedDcrs->count() }})
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impact</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingDcrs as $dcr)
                                    <tr class="dcr-row" data-impact="{{ $dcr->impact_rating }}" data-escalated="{{ $dcr->auto_escalated ? 'true' : 'false' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="font-medium">{{ $dcr->dcr_id }}</span>
                                                @if($dcr->auto_escalated)
                                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Escalated</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->request_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $dcr->author->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $dcr->author->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($dcr->impact_rating)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $dcr->impact_rating === 'High' ? 'bg-red-100 text-red-800' : 
                                                       ($dcr->impact_rating === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                                                       'bg-green-100 text-green-800') }}">
                                                    {{ $dcr->impact_rating }}
                                                </span>
                                            @else
                                                <button onclick="showImpactRatingModal({{ $dcr->id }})" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    Rate Impact
                                                </button>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="{{ $dcr->due_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                                {{ $dcr->due_date->toFormattedDateString() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('dcr.show', $dcr) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                                
                                                @if($dcr->impact_rating)
                                                    <button onclick="showApprovalModal({{ $dcr->id }}, 'approve')" 
                                                            class="text-green-600 hover:text-green-900">Approve</button>
                                                    <button onclick="showApprovalModal({{ $dcr->id }}, 'reject')" 
                                                            class="text-red-600 hover:text-red-900">Reject</button>
                                                    <button onclick="showApprovalModal({{ $dcr->id }}, 'approve-with-recs')" 
                                                            class="text-purple-600 hover:text-purple-900">Approve with Recs</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($pendingDcrs->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending DCRs</h3>
                            <p class="mt-1 text-sm text-gray-500">All DCRs have been processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Impact Rating Modal -->
    <div id="impactRatingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Impact Rating</h3>
                <div class="mt-2 px-7 py-3">
                    <div class="space-y-2">
                        <button onclick="quickRateImpact('Low')" class="w-full text-left px-3 py-2 border rounded hover:bg-green-50">
                            <span class="font-medium">Low Impact</span> - Minimal operational impact
                        </button>
                        <button onclick="quickRateImpact('Medium')" class="w-full text-left px-3 py-2 border rounded hover:bg-yellow-50">
                            <span class="font-medium">Medium Impact</span> - Significant operational impact
                        </button>
                        <button onclick="quickRateImpact('High')" class="w-full text-left px-3 py-2 border rounded hover:bg-red-50">
                            <span class="font-medium">High Impact</span> - Critical operational impact
                        </button>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeImpactRatingModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle"></h3>
                <div class="mt-2 px-7 py-3">
                    <label class="block text-sm font-medium text-gray-700">Comments</label>
                    <textarea id="approvalComments" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Add your comments..."></textarea>
                    
                    <div id="recommendationsSection" class="mt-4 hidden">
                        <label class="block text-sm font-medium text-gray-700">Recommendations</label>
                        <textarea id="approvalRecommendations" rows="3" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Add recommendations for implementation..."></textarea>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeApprovalModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-2">
                        Cancel
                    </button>
                    <button id="confirmApproval" onclick="confirmApproval()" class="px-4 py-2 rounded-md text-white">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDcrId = null;
        let currentAction = null;

        function filterDcrs(filter) {
            const rows = document.querySelectorAll('.dcr-row');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Update button styles
            buttons.forEach(btn => {
                if (btn.dataset.filter === filter) {
                    btn.className = 'filter-btn px-3 py-1 text-sm font-medium rounded-md bg-white text-gray-700 shadow-sm';
                } else {
                    btn.className = 'filter-btn px-3 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700';
                }
            });
            
            // Filter rows
            rows.forEach(row => {
                const impact = row.dataset.impact;
                const escalated = row.dataset.escalated === 'true';
                
                let show = false;
                if (filter === 'all') {
                    show = true;
                } else if (filter === 'high') {
                    show = impact === 'High';
                } else if (filter === 'escalated') {
                    show = escalated;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }

        function showImpactRatingModal(dcrId) {
            currentDcrId = dcrId;
            document.getElementById('impactRatingModal').classList.remove('hidden');
        }

        function closeImpactRatingModal() {
            document.getElementById('impactRatingModal').classList.add('hidden');
            currentDcrId = null;
        }

        function quickRateImpact(rating) {
            window.location.href = `/dcr/${currentDcrId}/impact-rating?quick_rate=${rating}`;
        }

        function showApprovalModal(dcrId, action) {
            currentDcrId = dcrId;
            currentAction = action;
            
            const modal = document.getElementById('approvalModal');
            const title = document.getElementById('modalTitle');
            const confirmBtn = document.getElementById('confirmApproval');
            const recommendationsSection = document.getElementById('recommendationsSection');
            
            if (action === 'approve') {
                title.textContent = 'Approve DCR';
                confirmBtn.textContent = 'Approve';
                confirmBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700';
                recommendationsSection.classList.add('hidden');
            } else if (action === 'reject') {
                title.textContent = 'Reject DCR';
                confirmBtn.textContent = 'Reject';
                confirmBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700';
                recommendationsSection.classList.add('hidden');
            } else if (action === 'approve-with-recs') {
                title.textContent = 'Approve with Recommendations';
                confirmBtn.textContent = 'Approve with Recs';
                confirmBtn.className = 'px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700';
                recommendationsSection.classList.remove('hidden');
            }
            
            modal.classList.remove('hidden');
        }

        function closeApprovalModal() {
            document.getElementById('approvalModal').classList.add('hidden');
            document.getElementById('approvalComments').value = '';
            document.getElementById('approvalRecommendations').value = '';
            currentDcrId = null;
            currentAction = null;
        }

        function confirmApproval() {
            const comments = document.getElementById('approvalComments').value;
            const recommendations = document.getElementById('approvalRecommendations').value;
            
            let route;
            if (currentAction === 'approve') {
                route = `/dcr/${currentDcrId}/approve`;
            } else if (currentAction === 'reject') {
                route = `/dcr/${currentDcrId}/reject`;
            } else if (currentAction === 'approve-with-recs') {
                route = `/dcr/${currentDcrId}/approve-with-recommendations`;
            }
            
            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    comments: comments,
                    recommendations: recommendations
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeApprovalModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while processing the request', 'error');
            });
        }
    </script>
</x-app-layout>
