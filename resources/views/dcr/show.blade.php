<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('DCR Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('dcr.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to My DCRs
                </a>
            </div>

            <!-- DCR Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header Section -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $dcr->dcr_id }}</h1>
                                <p class="mt-1 text-sm text-gray-500">
                                    Submitted on {{ $dcr->created_at->toFormattedDateString() }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $dcr->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($dcr->status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                       ($dcr->status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                       'bg-gray-100 text-gray-800')) }}">
                                    {{ $dcr->status }}
                                </span>
                                @if($dcr->due_date->isPast() && $dcr->status === 'Pending')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Overdue
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Request Type</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $dcr->request_type }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Due Date</h3>
                            <p class="text-lg font-semibold text-gray-900 {{ $dcr->due_date->isPast() && $dcr->status === 'Pending' ? 'text-red-600' : '' }}">
                                {{ $dcr->due_date->toFormattedDateString() }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Author</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $dcr->author->name }}</p>
                            <p class="text-sm text-gray-500">{{ $dcr->author->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Assigned To</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $dcr->recipient->name }}</p>
                            <p class="text-sm text-gray-500">{{ $dcr->recipient->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Impact Rating</h3>
                            @if($dcr->impact_rating)
                                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full 
                                    {{ $dcr->impact_rating === 'High' ? 'bg-red-100 text-red-800' : 
                                       ($dcr->impact_rating === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-green-100 text-green-800') }}">
                                    {{ $dcr->impact_rating }} Impact
                                </span>
                            @else
                                <span class="text-gray-400">Not rated</span>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $dcr->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($dcr->status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                   ($dcr->status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                   'bg-gray-100 text-gray-800')) }}">
                                {{ $dcr->status }}
                            </span>
                            @if($dcr->auto_escalated)
                                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                    Escalated to {{ $dcr->escalatedTo->name ?? 'Senior Management' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Reason for Change -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Reason for Change</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $dcr->reason_for_change }}</p>
                        </div>
                    </div>

                    <!-- Impact Summary -->
                    @if($dcr->impact_summary)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Impact Summary</h3>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $dcr->impact_summary }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Recommendations -->
                    @if($dcr->recommendations)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Recommendations</h3>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $dcr->recommendations }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if($dcr->attachments && !empty(json_decode($dcr->attachments)))
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Attachments</h3>
                            <div class="space-y-2">
                                @php
                                    $attachments = json_decode($dcr->attachments);
                                @endphp
                                @foreach($attachments as $attachment)
                                    @php
                                        $filename = basename($attachment);
                                        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                        $isPdf = $extension === 'pdf';
                                    @endphp
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center">
                                            @if($isPdf)
                                                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                            @endif
                                            <span class="text-sm text-gray-900">{{ $filename }}</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($isPdf)
                                                <a href="{{ route('dcr.pdf.view', [$dcr, $filename]) }}" 
                                                   target="_blank"
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    View PDF
                                                </a>
                                            @endif
                                            <a href="{{ asset('storage/' . $attachment) }}" 
                                               download="{{ $filename }}"
                                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons (for recipients and admins) -->
                    @if((auth()->user()->id === $dcr->recipient_id || auth()->user()->role === 'Admin') && $dcr->status === 'Pending')
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                            <div class="flex flex-wrap gap-3">
                                @if(!$dcr->impact_rating)
                                    <a href="{{ route('dcr.impact.rating', $dcr) }}" 
                                       class="px-4 py-2 bg-yellow-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        Rate Impact
                                    </a>
                                @endif
                                <button onclick="showActionModal('approve')" 
                                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Approve
                                </button>
                                <button onclick="showActionModal('reject')" 
                                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Reject
                                </button>
                                @if($dcr->impact_rating)
                                    <button onclick="showActionModal('approve-with-recs')" 
                                            class="px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Approve with Recommendations
                                    </button>
                                @endif
                                <a href="{{ route('dcr.approval.dashboard') }}" 
                                   class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Approval Dashboard
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Status History (if implemented) -->
                    @if($dcr->updated_at > $dcr->created_at)
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status History</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">
                                        Last updated on {{ $dcr->updated_at->toFormattedDateString() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal for Approve/Reject -->
    <div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle"></h3>
                <div class="mt-2 px-7 py-3">
                    <label class="block text-sm font-medium text-gray-700">Comments</label>
                    <textarea id="actionComments" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Add your comments (optional)..."></textarea>
                    
                    <div id="recommendationsSection" class="mt-4 hidden">
                        <label class="block text-sm font-medium text-gray-700">Recommendations</label>
                        <textarea id="actionRecommendations" rows="3" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Add recommendations for implementation..."></textarea>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeActionModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 mr-2">
                        Cancel
                    </button>
                    <button id="confirmAction" onclick="confirmAction()" 
                            class="px-4 py-2 rounded-md text-white">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAction = 'approve';

        function showActionModal(action) {
            currentAction = action;
            
            const modal = document.getElementById('actionModal');
            const title = document.getElementById('modalTitle');
            const confirmBtn = document.getElementById('confirmAction');
            const recommendationsSection = document.getElementById('recommendationsSection');
            
            if (action === 'approve') {
                title.textContent = `Approve DCR {{ $dcr->dcr_id }}`;
                confirmBtn.textContent = 'Approve';
                confirmBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700';
                recommendationsSection.classList.add('hidden');
            } else if (action === 'reject') {
                title.textContent = `Reject DCR {{ $dcr->dcr_id }}`;
                confirmBtn.textContent = 'Reject';
                confirmBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700';
                recommendationsSection.classList.add('hidden');
            } else if (action === 'approve-with-recs') {
                title.textContent = `Approve DCR {{ $dcr->dcr_id }} with Recommendations`;
                confirmBtn.textContent = 'Approve with Recs';
                confirmBtn.className = 'px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700';
                recommendationsSection.classList.remove('hidden');
            }
            
            modal.classList.remove('hidden');
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionComments').value = '';
        }

        function confirmAction() {
            const comments = document.getElementById('actionComments').value;
            const recommendations = document.getElementById('actionRecommendations') ? document.getElementById('actionRecommendations').value : '';
            
            // Make AJAX call to update the DCR status
            let route;
            if (currentAction === 'approve') {
                route = `/dcr/{{ $dcr->id }}/approve`;
            } else if (currentAction === 'reject') {
                route = `/dcr/{{ $dcr->id }}/reject`;
            } else if (currentAction === 'approve-with-recs') {
                route = `/dcr/{{ $dcr->id }}/approve-with-recommendations`;
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
                    closeActionModal();
                    // Refresh the page after a short delay
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
