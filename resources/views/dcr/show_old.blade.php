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
                                    Submitted on {{ $dcr->created_at ? $dcr->created_at->format('F j, Y') : 'N/A' }}
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
                                @if($dcr->due_date && $dcr->due_date->isPast() && $dcr->status === 'Pending')
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
                            <p class="text-lg font-semibold text-gray-900 {{ $dcr->due_date && $dcr->due_date->isPast() && $dcr->status === 'Pending' ? 'text-red-600' : '' }}">
                                {{ $dcr->due_date ? $dcr->due_date->format('F j, Y') : 'Not set' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Author</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $dcr->author->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $dcr->author->email ?? '' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Assigned To</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $dcr->recipient->name ?? 'Not assigned' }}</p>
                            <p class="text-sm text-gray-500">{{ $dcr->recipient->email ?? '' }}</p>
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

                        <!-- Compliance Section -->
                        @if(auth()->user()->isAdministrator() || auth()->user()->isDecisionMaker())
                            <div class="border-t border-gray-200 pt-6 mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Compliance & Verification</h3>
                                
                                <!-- Compliance Status -->
                                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Verified</span>
                                            <div class="mt-1">
                                                @if($dcr->is_verified)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✓ Yes
                                                    </span>
                                                    <p class="text-xs text-gray-500 mt-1">by {{ $dcr->verifiedBy->name ?? 'N/A' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $dcr->verified_at?->format('M d, Y') }}</p>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        No
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Validated</span>
                                            <div class="mt-1">
                                                @if($dcr->is_validated)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✓ Yes
                                                    </span>
                                                    <p class="text-xs text-gray-500 mt-1">by {{ $dcr->validatedBy->name ?? 'N/A' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $dcr->validated_at?->format('M d, Y') }}</p>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        No
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Record Status</span>
                                            <div class="mt-1">
                                                @if($dcr->is_locked)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        🔒 Locked
                                                    </span>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $dcr->lock_reason }}</p>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Unlocked
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Closure Status</span>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $dcr->closure_status === 'Closed' ? 'bg-gray-800 text-white' : 
                                                       ($dcr->closure_status === 'Pending_Closure' ? 'bg-yellow-100 text-yellow-800' : 
                                                       'bg-blue-100 text-blue-800') }}">
                                                    {{ str_replace('_', ' ', $dcr->closure_status) }}
                                                </span>
                                                @if($dcr->closure_status === 'Closed')
                                                    <p class="text-xs text-gray-500 mt-1">by {{ $dcr->closedBy->name ?? 'N/A' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $dcr->closed_at?->format('M d, Y') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Compliance Actions -->
                                @if($dcr->closure_status !== 'Closed')
                                    <div class="flex flex-wrap gap-3">
                                        @if(!$dcr->is_verified)
                                            <form action="{{ route('dcr.verify', $dcr) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                                                    ✓ Verify DCR
                                                </button>
                                            </form>
                                        @endif

                                        @if($dcr->is_verified && !$dcr->is_validated)
                                            <form action="{{ route('dcr.validate', $dcr) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700">
                                                    ✓ Validate DCR
                                                </button>
                                            </form>
                                        @endif

                                        @if($dcr->is_verified && $dcr->is_validated && in_array($dcr->status, ['Completed', 'Approved']))
                                            <button onclick="showComplianceModal('closure')" 
                                                    class="px-4 py-2 bg-gray-800 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-900">
                                                🔒 Process Closure
                                            </button>
                                        @endif

                                        @if(!$dcr->is_locked)
                                            <button onclick="showComplianceModal('lock')" 
                                                    class="px-4 py-2 bg-yellow-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-yellow-700">
                                                🔒 Lock Record
                                            </button>
                                        @elseif(auth()->user()->isAdministrator())
                                            <button onclick="showComplianceModal('unlock')" 
                                                    class="px-4 py-2 bg-orange-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-orange-700">
                                                🔓 Unlock Record
                                            </button>
                                        @endif

                                        @if(!$dcr->is_archived && $dcr->status === 'Completed')
                                            <form action="{{ route('dcr.archive', $dcr) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Archive all documents for this DCR?')"
                                                        class="px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700">
                                                    📦 Archive Documents
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Record is Closed</h3>
                                                <p class="mt-1 text-sm text-red-700">
                                                    This DCR has been closed and locked for compliance. No modifications are permitted.
                                                </p>
                                                @if($dcr->closure_notes)
                                                    <p class="mt-2 text-sm text-red-700">
                                                        <strong>Closure Notes:</strong> {{ $dcr->closure_notes }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <!-- Read-Only Warning -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Record Locked</h3>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            This DCR is locked and cannot be modified. {{ $dcr->lock_reason }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
                    form.action = "{{ route('dcr.approve', $dcr->id ?? 0) }}";
                    document.getElementById('commentsSection').classList.remove('hidden');
                    btn.textContent = 'Approve DCR';
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');
                    break;
                case 'reject':
                    title.textContent = 'Reject DCR';
                    form.action = "{{ route('dcr.reject', $dcr->id ?? 0) }}";
                    document.getElementById('commentsSection').classList.remove('hidden');
                    btn.textContent = 'Reject DCR';
                    btn.classList.add('bg-red-600', 'hover:bg-red-700');
                    break;
                case 'approve-with-recs':
                    title.textContent = 'Approve with Recommendations';
                    form.action = "{{ route('dcr.approve.with.recommendations', $dcr->id ?? 0) }}";
                    document.getElementById('commentsSection').classList.remove('hidden');
                    document.getElementById('recommendationsSection').classList.remove('hidden');
                    btn.textContent = 'Approve & Task';
                    btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
                    break;
                case 'reassign':
                    title.textContent = 'Reassign DCR Recipient';
                    form.action = "{{ route('dcr.reassign', $dcr->id ?? 0) }}";
                    document.getElementById('reassignSection').classList.remove('hidden');
                    btn.textContent = 'Reassign Task';
                    btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    break;
                case 'complete':
                    title.textContent = 'Mark Implementation as Complete';
                    form.action = "{{ route('dcr.complete', $dcr->id ?? 0) }}";
                    document.getElementById('completeSection').classList.remove('hidden');
                    btn.textContent = 'Submit Completion';
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    break;
                case 'close':
                    title.textContent = 'Final DCR Verification & Closure';
                    form.action = "{{ route('dcr.close', $dcr->id ?? 0) }}";
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

        function showComplianceModal(action) {
            const modal = document.getElementById('complianceModal');
            const form = document.getElementById('complianceForm');
            const title = document.getElementById('complianceModalTitle');
            const btn = document.getElementById('confirmComplianceBtn');
            const sections = ['verifySection', 'validateSection', 'closureSection', 'lockSection', 'unlockSection'];
            
            // Hide all sections first
            sections.forEach(s => {
                const el = document.getElementById(s);
                if (el) el.classList.add('hidden');
            });
            
            // Reset button classes
            btn.className = 'px-4 py-2 rounded-md text-white font-medium shadow-sm transition';

            switch(action) {
                case 'verify':
                    title.textContent = 'Verify DCR Implementation';
                    form.action = "{{ route('dcr.verify', $dcr->id ?? 0) }}";
                    document.getElementById('verifySection').classList.remove('hidden');
                    btn.textContent = 'Verify Implementation';
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    break;
                case 'validate':
                    title.textContent = 'Validate DCR Compliance';
                    form.action = "{{ route('dcr.validate', $dcr->id ?? 0) }}";
                    document.getElementById('validateSection').classList.remove('hidden');
                    btn.textContent = 'Validate Compliance';
                    btn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    break;
                case 'closure':
                    title.textContent = 'Close DCR';
                    form.action = "{{ route('dcr.closure', $dcr->id ?? 0) }}";
                    document.getElementById('closureSection').classList.remove('hidden');
                    btn.textContent = 'Close DCR';
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');
                    break;
                case 'lock':
                    title.textContent = 'Lock DCR Record';
                    form.action = "{{ route('dcr.lock', $dcr->id ?? 0) }}";
                    document.getElementById('lockSection').classList.remove('hidden');
                    btn.textContent = 'Lock Record';
                    btn.classList.add('bg-red-600', 'hover:bg-red-700');
                    break;
                case 'unlock':
                    title.textContent = 'Unlock DCR Record';
                    form.action = "{{ route('dcr.unlock', $dcr->id ?? 0) }}";
                    document.getElementById('unlockSection').classList.remove('hidden');
                    btn.textContent = 'Unlock Record';
                    btn.classList.add('bg-amber-600', 'hover:bg-amber-700');
                    break;
            }
            
            modal.classList.remove('hidden');
        }

        function closeComplianceModal() {
            document.getElementById('complianceModal').classList.add('hidden');
            document.getElementById('complianceForm').reset();
        }
    </script>

    <!-- Compliance Modal -->
    <div id="complianceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <form id="complianceForm" method="POST">
                @csrf
                <div class="mt-3">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2" id="complianceModalTitle">Compliance Action</h3>
                    
                    <!-- Verify Section -->
                    <div id="verifySection" class="mt-4 hidden">
                        <div>
                            <label for="verification_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Verification Notes <span class="text-red-500">*</span>
                            </label>
                            <textarea name="verification_notes" id="verification_notes" rows="4" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                    placeholder="Document your verification findings, confirm implementation meets requirements..."></textarea>
                        </div>
                    </div>

                    <!-- Validate Section -->
                    <div id="validateSection" class="mt-4 hidden">
                        <div>
                            <label for="validation_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Validation Notes <span class="text-red-500">*</span>
                            </label>
                            <textarea name="validation_notes" id="validation_notes" rows="4" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                                    placeholder="Document compliance validation, confirm all standards are met..."></textarea>
                        </div>
                    </div>

                    <!-- Closure Section -->
                    <div id="closureSection" class="mt-4 hidden">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Closure Checklist <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-2 bg-gray-50 p-3 rounded-md">
                                <label class="flex items-center">
                                    <input type="checkbox" name="closure_checklist[]" value="all_documents_uploaded" required
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                    <span class="ml-2 text-sm text-gray-700">All documents uploaded and verified</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="closure_checklist[]" value="impact_assessment_complete" required
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                    <span class="ml-2 text-sm text-gray-700">Impact assessment completed</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="closure_checklist[]" value="stakeholders_notified" required
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                    <span class="ml-2 text-sm text-gray-700">All stakeholders notified</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="closure_checklist[]" value="implementation_verified" required
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                    <span class="ml-2 text-sm text-gray-700">Implementation verified and validated</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="closure_checklist[]" value="no_outstanding_issues" required
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                    <span class="ml-2 text-sm text-gray-700">No outstanding issues or dependencies</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label for="closure_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Closure Notes <span class="text-red-500">*</span>
                            </label>
                            <textarea name="closure_notes" id="closure_notes" rows="4" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                                    placeholder="Document final closure summary, outcomes, and any lessons learned..."></textarea>
                        </div>
                    </div>

                    <!-- Lock Section -->
                    <div id="lockSection" class="mt-4 hidden">
                        <div class="bg-amber-50 border border-amber-200 rounded-md p-3 mb-4">
                            <p class="text-sm text-amber-800">
                                <strong>Warning:</strong> Locking this record will prevent any further modifications. Only unlock if absolutely necessary.
                            </p>
                        </div>
                        <div>
                            <label for="lock_reason" class="block text-sm font-medium text-gray-700 mb-1">
                                Lock Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="lock_reason" id="lock_reason" rows="3" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring focus:ring-red-200"
                                    placeholder="Explain why this record needs to be locked..."></textarea>
                        </div>
                    </div>

                    <!-- Unlock Section -->
                    <div id="unlockSection" class="mt-4 hidden">
                        <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                            <p class="text-sm text-red-800">
                                <strong>Caution:</strong> Unlocking allows modifications to a closed record. This should only be done with proper authorization.
                            </p>
                        </div>
                        <div>
                            <label for="unlock_reason" class="block text-sm font-medium text-gray-700 mb-1">
                                Unlock Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="unlock_reason" id="unlock_reason" rows="3" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200"
                                    placeholder="Provide justification for unlocking this record..."></textarea>
                        </div>
                    </div>

                    <div class="items-center px-4 py-3 border-t mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeComplianceModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" id="confirmComplianceBtn" 
                                class="px-4 py-2 rounded-md text-white font-medium shadow-sm transition">
                            Confirm Action
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
