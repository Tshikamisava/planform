<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My DCRs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Actions -->
            @if(auth()->user()->role === 'Author' || auth()->user()->role === 'Admin')
                <div class="mb-6">
                    <a href="{{ route('dcr.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Submit New DCR
                    </a>
                </div>
            @endif

            <!-- Submitted DCRs (for Authors and Admins) -->
            @if(auth()->user()->role === 'Author' || auth()->user()->role === 'Admin')
                @if($submittedDcrs->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Submitted DCRs</h3>
                            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($submittedDcrs as $dcr)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $dcr->dcr_id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->request_type }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->recipient->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->created_at->toFormattedDateString() }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->due_date->toFormattedDateString() }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $dcr->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                           ($dcr->status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                                           ($dcr->status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                                           'bg-gray-100 text-gray-800')) }}">
                                                        {{ $dcr->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('dcr.show', $dcr) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-center text-gray-500">
                            <p class="text-lg">No submitted DCRs found.</p>
                            <p class="text-sm mt-2">Submit your first DCR to get started.</p>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Assigned DCRs (for Recipients and Admins) -->
            @if(auth()->user()->role === 'Recipient' || auth()->user()->role === 'Admin')
                @if($assignedDcrs->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold mb-4">Assigned DCRs</h3>
                            <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($assignedDcrs as $dcr)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $dcr->dcr_id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->request_type }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->author->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $dcr->created_at->toFormattedDateString() }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="{{ $dcr->due_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                                        {{ $dcr->due_date->toFormattedDateString() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $dcr->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                           ($dcr->status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                                           ($dcr->status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                                           'bg-gray-100 text-gray-800')) }}">
                                                        {{ $dcr->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('dcr.show', $dcr) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                                    @if($dcr->status === 'Pending')
                                                        <button onclick="showActionModal('{{ $dcr->id }}', '{{ $dcr->dcr_id }}')" 
                                                                class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                                                        <button onclick="showActionModal('{{ $dcr->id }}', '{{ $dcr->dcr_id }}', 'reject')" 
                                                                class="text-red-600 hover:text-red-900">Reject</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500">
                            <p class="text-lg">No assigned DCRs found.</p>
                            <p class="text-sm mt-2">DCRs assigned to you will appear here.</p>
                        </div>
                    </div>
                @endif
            @endif
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
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
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
        let currentDcrId = null;
        let currentAction = 'approve';

        function showActionModal(dcrId, dcrNumber, action = 'approve') {
            currentDcrId = dcrId;
            currentAction = action;
            
            const modal = document.getElementById('actionModal');
            const title = document.getElementById('modalTitle');
            const confirmBtn = document.getElementById('confirmAction');
            
            if (action === 'approve') {
                title.textContent = `Approve DCR ${dcrNumber}`;
                confirmBtn.textContent = 'Approve';
                confirmBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700';
            } else {
                title.textContent = `Reject DCR ${dcrNumber}`;
                confirmBtn.textContent = 'Reject';
                confirmBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700';
            }
            
            modal.classList.remove('hidden');
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionComments').value = '';
        }

        function confirmAction() {
            const comments = document.getElementById('actionComments').value;
            
            // Make AJAX call to update the DCR status
            const route = currentAction === 'approve' 
                ? `/dcr/${currentDcrId}/approve`
                : `/dcr/${currentDcrId}/reject`;
            
            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    comments: comments
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
