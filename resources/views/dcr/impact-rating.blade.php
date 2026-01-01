<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Impact Rating Assessment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- DCR Summary Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">DCR Summary</h3>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $dcr->dcr_id }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Request Type</p>
                            <p class="font-medium">{{ $dcr->request_type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Submitted By</p>
                            <p class="font-medium">{{ $dcr->author->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Assigned To</p>
                            <p class="font-medium">{{ $dcr->recipient->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Due Date</p>
                            <p class="font-medium">{{ $dcr->due_date->toFormattedDateString() }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Reason for Change</p>
                        <p class="text-gray-900 mt-1">{{ Str::limit($dcr->reason_for_change, 200) }}</p>
                    </div>
                </div>
            </div>

            <!-- Impact Rating Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Impact Assessment</h3>
                    
                    <form action="{{ route('dcr.impact.store', $dcr) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Impact Rating Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4">
                                Impact Rating <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="space-y-3">
                                <!-- Low Impact -->
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('impact_rating') === 'Low' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input type="radio" name="impact_rating" value="Low" 
                                           {{ old('impact_rating') === 'Low' ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500 mr-3"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">Low Impact</span>
                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Low Risk</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Minimal operational impact, easily reversible changes</p>
                                    </div>
                                </label>

                                <!-- Medium Impact -->
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('impact_rating') === 'Medium' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input type="radio" name="impact_rating" value="Medium" 
                                           {{ old('impact_rating') === 'Medium' ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500 mr-3"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">Medium Impact</span>
                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Moderate Risk</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Significant operational impact, requires careful planning</p>
                                    </div>
                                </label>

                                <!-- High Impact -->
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('impact_rating') === 'High' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                                    <input type="radio" name="impact_rating" value="High" 
                                           {{ old('impact_rating') === 'High' ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500 mr-3"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">High Impact</span>
                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">High Risk</span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Critical operational impact, may require senior management approval</p>
                                    </div>
                                </label>
                            </div>

                            @error('impact_rating')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Impact Summary -->
                        <div>
                            <label for="impact_summary" class="block text-sm font-medium text-gray-700 mb-2">
                                Impact Summary <span class="text-red-500">*</span>
                            </label>
                            <textarea id="impact_summary" name="impact_summary" rows="4" required
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Describe the potential impact of this change on operations, systems, and stakeholders...">{{ old('impact_summary') }}</textarea>
                            @error('impact_summary')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Recommendations -->
                        <div>
                            <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                                Recommendations
                            </label>
                            <textarea id="recommendations" name="recommendations" rows="4"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Provide recommendations for implementing this change safely and effectively...">{{ old('recommendations') }}</textarea>
                            @error('recommendations')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Auto-Escalation Notice -->
                        <input type="hidden" name="auto_escalate" id="auto_escalate" value="0">
                        <div id="escalation-notice" class="hidden p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Auto-Escalation Notice</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This DCR will be automatically escalated to senior management due to its high impact rating.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('dcr.show', $dcr) }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Impact Assessment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle auto-escalation notice visibility
        document.addEventListener('DOMContentLoaded', function() {
            const impactRadios = document.querySelectorAll('input[name="impact_rating"]');
            const escalationNotice = document.getElementById('escalation-notice');
            const autoEscalateInput = document.getElementById('auto_escalate');

            function checkEscalation() {
                const selectedImpact = document.querySelector('input[name="impact_rating"]:checked');
                if (selectedImpact && selectedImpact.value === 'High') {
                    escalationNotice.classList.remove('hidden');
                    autoEscalateInput.value = '1';
                } else {
                    escalationNotice.classList.add('hidden');
                    autoEscalateInput.value = '0';
                }
            }

            impactRadios.forEach(radio => {
                radio.addEventListener('change', checkEscalation);
            });

            // Check initial state
            checkEscalation();
        });
    </script>
</x-app-layout>
