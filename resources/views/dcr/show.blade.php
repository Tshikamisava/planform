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
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Original Attachments</h3>
                            <div class="grid grid-cols-1 gap-2">
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

                    <!-- Implementation Details -->
                    @if($dcr->implementation_notes)
                        <div class="mb-6 border-t border-gray-100 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Implementation Details</h3>
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <p class="text-sm font-medium text-gray-500 mb-1">Implementation Notes</p>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $dcr->implementation_notes }}</p>
                            </div>
                            
                            @if($dcr->completed_attachments && !empty(json_decode($dcr->completed_attachments)))
                                <p class="text-sm font-medium text-gray-500 mb-2">Completed Documents</p>
                                <div class="grid grid-cols-1 gap-2">
                                    @php
                                        $compAttachments = json_decode($dcr->completed_attachments);
                                    @endphp
                                    @foreach($compAttachments as $attachment)
                                        @php $filename = basename($attachment); @endphp
                                        <div class="flex items-center justify-between bg-blue-50 rounded-lg p-3">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-900">{{ $filename }}</span>
                                            </div>
                                            <a href="{{ asset('storage/' . $attachment) }}" 
                                               download="{{ $filename }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Download
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                     <!-- Action Buttons -->
                    @if(!$dcr->is_locked)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Task Management</h3>
                            <div class="flex flex-wrap gap-3">
                                <!-- Pending Actions (Rating & Approval) -->
                                @if((auth()->user()->id === $dcr->recipient_id || auth()->user()->role === 'Admin') && $dcr->status === 'Pending')
                                    @if(!$dcr->impact_rating)
                                        <a href="{{ route('dcr.impact.rating', $dcr) }}" 
                                           class="px-4 py-2 bg-yellow-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-yellow-700">
                                            Rate Impact
                                        </a>
                                    @endif
                                    <button onclick="showActionModal('approve')" 
                                            class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button onclick="showActionModal('reject')" 
                                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700">
                                        Reject
                                    </button>
                                    @if($dcr->impact_rating)
                                        <button onclick="showActionModal('approve-with-recs')" 
                                                class="px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700">
                                            Approve with Recommendations
                                        </button>
                                    @endif
                                @endif

                                <!-- Reassignment (DOM/Admin only) -->
                                @if((auth()->user()->role === 'DOM' || auth()->user()->role === 'Admin') && in_array($dcr->status, ['Pending', 'Approved']))
                                    <button onclick="showActionModal('reassign')" 
                                            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700">
                                        Reassign Task
                                    </button>
                                @endif

                                <!-- Implementation Tracking (Recipient Only) -->
                                @if((auth()->user()->id === $dcr->recipient_id || auth()->user()->role === 'Admin') && $dcr->status === 'Approved')
                                    <button onclick="showActionModal('complete')" 
                                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                                        Mark as Complete
                                    </button>
                                @endif

                                <!-- Final Closure (DOM/Admin Only) -->
                                @if((auth()->user()->role === 'DOM' || auth()->user()->role === 'Admin') && $dcr->status === 'Completed')
                                    <button onclick="showActionModal('close')" 
                                            class="px-4 py-2 bg-black border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-800">
                                        Verify & Close DCR
                                    </button>
                                @endif
                                
                                <a href="{{ route('dcr.dashboard') }}" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Return to List
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-8 p-4 bg-gray-100 border-l-4 border-gray-500 rounded text-gray-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-bold">This record is archived and locked.</span>
                            </div>
                            <p class="text-sm mt-1">Further modifications are prohibited for audit purposes.</p>
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

    <!-- Action Modal -->
    <div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <form id="actionForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2" id="modalTitle">Task Action</h3>
                    
                    <div class="mt-4 px-2 space-y-4">
                        <!-- Standard Comments (for Approve/Reject) -->
                        <div id="commentsSection">
                            <label class="block text-sm font-medium text-gray-700">Comments</label>
                            <textarea name="comments" rows="3" 
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Add your comments..."></textarea>
                        </div>
                        
                        <!-- Recommendations (for Approve with Recs) -->
                        <div id="recommendationsSection" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">Recommendations <span class="text-red-500">*</span></label>
                            <textarea name="recommendations" rows="3" 
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Provide mandatory implementation steps..."></textarea>
                        </div>

                        <!-- Reassignment (Dropdown) -->
                        <div id="reassignSection" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">New Recipient <span class="text-red-500">*</span></label>
                            <select name="recipient_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Select User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Completion Notes & Uploads -->
                        <div id="completeSection" class="hidden">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Implementation Notes <span class="text-red-500">*</span></label>
                                <textarea name="implementation_notes" rows="3" 
                                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Describe what has been changed..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Upload Evidence/Documents</label>
                                <input type="file" name="completed_attachments[]" multiple 
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-400 mt-1">PDF, Images, or Office docs allowed.</p>
                            </div>
                        </div>

                        <!-- Closure Confirmation -->
                        <div id="closeSection" class="hidden">
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-800 text-sm">
                                <p class="font-bold">Final Verification Check:</p>
                                <ul class="list-disc ml-4 mt-2">
                                    <li>Implementation notes provided?</li>
                                    <li>Evidence documents uploaded?</li>
                                    <li>Physical drawings updated?</li>
                                </ul>
                                <p class="mt-3">Closing this DCR will <strong>lock</strong> the record permanently.</p>
                            </div>
                        </div>
                    </div>

                    <div class="items-center px-4 py-3 border-t mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeActionModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" id="confirmActionBtn" 
                                class="px-4 py-2 rounded-md text-white font-medium shadow-sm transition">
                            Confirm Action
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showActionModal(action) {
            const modal = document.getElementById('actionModal');
            const form = document.getElementById('actionForm');
            const title = document.getElementById('modalTitle');
            const btn = document.getElementById('confirmActionBtn');
            const sections = ['commentsSection', 'recommendationsSection', 'reassignSection', 'completeSection', 'closeSection'];
            
            // Hide all sections first
            sections.forEach(s => document.getElementById(s).classList.add('hidden'));
            
            // Reset button classes
            btn.className = 'px-4 py-2 rounded-md text-white font-medium shadow-sm transition';

            switch(action) {
                case 'approve':
                    title.textContent = 'Approve DCR';
                    form.action = "{{ route('dcr.approve', $dcr) }}";
                    document.getElementById('commentsSection').classList.remove('hidden');
                    btn.textContent = 'Approve DCR';
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');
                    break;
                case 'reject':
                    title.textContent = 'Reject DCR';
                    form.action = "{{ route('dcr.reject', $dcr) }}";
                    document.getElementById('commentsSection').classList.remove('hidden');
                    btn.textContent = 'Reject DCR';
                    btn.classList.add('bg-red-600', 'hover:bg-red-700');
                    break;
                case 'approve-with-recs':
                    title.textContent = 'Approve with Recommendations';
                    form.action = "{{ route('dcr.approve.with.recommendations', $dcr) }}";
                    document.getElementById('commentsSection').classList.remove('hidden');
                    document.getElementById('recommendationsSection').classList.remove('hidden');
                    btn.textContent = 'Approve & Task';
                    btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
                    break;
                case 'reassign':
                    title.textContent = 'Reassign DCR Recipient';
                    form.action = "{{ route('dcr.reassign', $dcr) }}";
                    document.getElementById('reassignSection').classList.remove('hidden');
                    btn.textContent = 'Reassign Task';
                    btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    break;
                case 'complete':
                    title.textContent = 'Mark Implementation as Complete';
                    form.action = "{{ route('dcr.complete', $dcr) }}";
                    document.getElementById('completeSection').classList.remove('hidden');
                    btn.textContent = 'Submit Completion';
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    break;
                case 'close':
                    title.textContent = 'Final DCR Verification & Closure';
                    form.action = "{{ route('dcr.close', $dcr) }}";
                    document.getElementById('closeSection').classList.remove('hidden');
                    btn.textContent = 'Verify & Close';
                    btn.classList.add('bg-black', 'hover:bg-gray-800');
                    break;
            }
            
            modal.classList.remove('hidden');
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionForm').reset();
        }
    </script>
</x-app-layout>
