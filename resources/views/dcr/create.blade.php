<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit New Design Change Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Display session errors -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('dcr.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Basic Information Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Submission Date (Read-only) -->
                                <div>
                                    <label for="submission_date" class="block text-sm font-medium text-gray-700">Submission Date</label>
                                    <input type="text" id="submission_date" name="submission_date" value="{{ now()->toFormattedDateString() }}" readonly class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Author (Read-only) -->
                                <div>
                                    <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
                                    <input type="text" id="author" name="author" value="{{ auth()->user()->name }}" readonly class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- DCR Details Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">DCR Details</h3>
                            
                            <!-- Request Type -->
                            <div class="mb-4">
                                <label for="request_type" class="block text-sm font-medium text-gray-700">Request Type <span class="text-red-500">*</span></label>
                                <select id="request_type" name="request_type" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Select Request Type</option>
                                    <option value="Design Change" {{ old('request_type') == 'Design Change' ? 'selected' : '' }}>Design Change</option>
                                    <option value="Process Improvement" {{ old('request_type') == 'Process Improvement' ? 'selected' : '' }}>Process Improvement</option>
                                    <option value="Safety Enhancement" {{ old('request_type') == 'Safety Enhancement' ? 'selected' : '' }}>Safety Enhancement</option>
                                    <option value="Cost Reduction" {{ old('request_type') == 'Cost Reduction' ? 'selected' : '' }}>Cost Reduction</option>
                                    <option value="Quality Improvement" {{ old('request_type') == 'Quality Improvement' ? 'selected' : '' }}>Quality Improvement</option>
                                    <option value="Other" {{ old('request_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('request_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reason for Change -->
                            <div class="mb-4">
                                <label for="reason_for_change" class="block text-sm font-medium text-gray-700">Reason for Change <span class="text-red-500">*</span></label>
                                <textarea id="reason_for_change" name="reason_for_change" rows="4" required
                                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Please provide a detailed reason for this change request...">{{ old('reason_for_change') }}</textarea>
                                @error('reason_for_change')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date <span class="text-red-500">*</span></label>
                                <input type="date" id="due_date" name="due_date" required
                                       min="{{ now()->toDateString() }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       value="{{ old('due_date') }}">
                                @error('due_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Assignment Section -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment</h3>
                            
                            <!-- Recipient -->
                            <div>
                                <label for="recipient_id" class="block text-sm font-medium text-gray-700">Assign To <span class="text-red-500">*</span></label>
                                <select id="recipient_id" name="recipient_id" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Select Recipient</option>
                                    @foreach ($users as $user)
                                        @if ($user->id !== auth()->id())
                                            <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->role }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('recipient_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Attachments Section -->
                        <div class="pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Attachments</h3>
                            
                            <div>
                                <label for="attachments" class="block text-sm font-medium text-gray-700">Supporting Documents</label>
                                <input type="file" id="attachments" name="attachments[]" multiple
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png"
                                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                <p class="mt-2 text-sm text-gray-500">
                                    Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max size: 2MB per file)
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('dcr.dashboard') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit DCR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
