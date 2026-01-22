@extends('layouts.app')

@section('title', 'Create New Design Change Request')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 relative pb-12">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-600 px-4 sm:px-6 py-3 sm:py-4">
            <h1 class="text-xl sm:text-2xl font-bold text-white">Create New Change Request</h1>
            <p class="text-blue-100 mt-1 text-sm sm:text-base">Submit a new DCR for review and approval</p>
        </div>

        <div class="p-4 sm:p-6">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error:</h3>
                            <div class="mt-1 text-sm text-red-700">
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('dcr.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Request / Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="Enter change request title"
                                   required>
                        </div>

                        <div>
                            <label for="request_type" class="block text-sm font-medium text-gray-700">
                                Request Type <span class="text-red-500">*</span>
                            </label>
                            <select id="request_type" 
                                    name="request_type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select type...</option>
                                <option value="Standard" {{ old('request_type') == 'Standard' ? 'selected' : '' }}>Standard</option>
                                <option value="Emergency" {{ old('request_type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                <option value="Routine" {{ old('request_type') == 'Routine' ? 'selected' : '' }}>Routine</option>
                                <option value="Corrective" {{ old('request_type') == 'Corrective' ? 'selected' : '' }}>Corrective</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">
                                Change Impact Rating <span class="text-red-500">*</span>
                            </label>
                            <select id="priority" 
                                    name="priority" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select impact level...</option>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Critical" {{ old('priority') == 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="due_date" 
                                   name="due_date" 
                                   value="{{ old('due_date') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   min="{{ now()->format('Y-m-d') }}"
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Author Information -->
                <div class="bg-blue-50 p-3 sm:p-4 rounded-lg border border-blue-200">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Author Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Author Name</label>
                            <div class="mt-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-900">
                                {{ auth()->user()->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Author Email</label>
                            <div class="mt-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-900">
                                {{ auth()->user()->email }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Submission Date</label>
                            <div class="mt-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-gray-900">
                                {{ now()->toFormattedDateString() }}
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-blue-700">The author name and submission date are automatically captured.</p>
                </div>

                <!-- Description -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Description*</h2>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Detailed Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Provide a comprehensive description of the change request, including what will be changed and how"
                                  required>{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Please be as detailed as possible (minimum 10 characters required)</p>
                    </div>
                </div>

                <!-- Reason for Change -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Reason for Change</h2>
                    
                    <div>
                        <label for="reason_for_change" class="block text-sm font-medium text-gray-700">
                            Reason for Change <span class="text-red-500">*</span>
                        </label>
                        <textarea id="reason_for_change" 
                                  name="reason_for_change" 
                                  rows="4" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Explain why this change is necessary"
                                  required>{{ old('reason_for_change') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Minimum 10 characters required</p>
                    </div>
                </div>

                <!-- Assignment -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Assignment</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="recipient_id" class="block text-sm font-medium text-gray-700">
                                Recipient <span class="text-red-500">*</span>
                            </label>
                            <select id="recipient_id" 
                                    name="recipient_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    required>
                                <option value="">Select recipient...</option>
                                @foreach ($recipients as $recipient)
                                    <option value="{{ $recipient->id }}" {{ old('recipient_id') == $recipient->id ? 'selected' : '' }}>
                                        {{ $recipient->name }} ({{ $recipient->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Person who will implement the change</p>
                        </div>

                        <div>
                            <label for="decision_maker_id" class="block text-sm font-medium text-gray-700">
                                Decision Maker (Optional)
                            </label>
                            <select id="decision_maker_id" 
                                    name="decision_maker_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select decision maker...</option>
                                @foreach ($decisionMakers as $decisionMaker)
                                    <option value="{{ $decisionMaker->id }}" {{ old('decision_maker_id') == $decisionMaker->id ? 'selected' : '' }}>
                                        {{ $decisionMaker->name }} ({{ $decisionMaker->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Person who will approve the change</p>
                        </div>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Attachments</h2>
                    
                    <div>
                        <label for="attachments" class="block text-sm font-medium text-gray-700">
                            Supporting Documents (Optional)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="attachments" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload files</span>
                                        <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR up to 10MB each</p>
                            </div>
                        </div>
                        <div id="file-list" class="mt-2 space-y-2"></div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0">
                    <a href="{{ route('dcr.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Cancel
                    </a>
                    
                    <div class="flex flex-col sm:flex-row gap-3 sm:space-x-3">
                        <button type="submit" name="action" value="save_draft" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors order-2 sm:order-1">
                            Save as Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors order-1 sm:order-2">
                            Submit for Review
                        </button>
                    </div>
                </div>
            </form>
        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('attachments');
    const fileList = document.getElementById('file-list');
    
    fileInput.addEventListener('change', function(e) {
        fileList.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm text-gray-700">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024).toFixed(2)} KB)</span>
                </div>
                <button type="button" class="text-red-500 hover:text-red-700" onclick="removeFile(${index})">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    });
    
    function removeFile(index) {
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        
        // Trigger change event to update display
        fileInput.dispatchEvent(new Event('change'));
    }
    
    // Auto-populate author info
    const authorInfo = {
        name: '{{ auth()->user()->name }}',
        email: '{{ auth()->user()->email }}',
        department: '{{ auth()->user()->department }}'
    };
    
    console.log('Author:', authorInfo);
});
</script>
@endsection
