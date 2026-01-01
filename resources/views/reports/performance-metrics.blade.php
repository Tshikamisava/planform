<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Performance Metrics Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Report Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Performance Metrics Report</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($startDate)->toFormattedDateString() }} - {{ Carbon\Carbon::parse($endDate)->toFormattedDateString() }}
                            </span>
                            <a href="{{ route('reports.export', ['type' => 'performance', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Time Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Avg Processing Time</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $processingMetrics->avg_processing_time ? round($processingMetrics->avg_processing_time, 1) : 0 }} days
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Fastest Processing</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $processingMetrics->min_processing_time ?? 0 }} days
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Slowest Processing</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $processingMetrics->max_processing_time ?? 0 }} days
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Processed</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $processingMetrics->total_processed ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Due Date Compliance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Due Date Compliance</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $dueDateCompliance->on_time ?? 0 }}</div>
                            <div class="text-sm text-gray-600">On Time</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $dueDateCompliance->total > 0 ? round(($dueDateCompliance->on_time / $dueDateCompliance->total) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $dueDateCompliance->overdue ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Overdue</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $dueDateCompliance->total > 0 ? round(($dueDateCompliance->overdue / $dueDateCompliance->total) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $dueDateCompliance->total ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total DCRs</div>
                            <div class="text-xs text-gray-500 mt-1">In selected period</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Performance Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Performance Metrics</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reviewed</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($userMetrics as $user)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                {{ $user->submitted_count ?? 0 }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                {{ $user->reviewed_count ?? 0 }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $user->role === 'Admin' ? 'bg-purple-100 text-purple-800' : 
                                                       ($user->role === 'Author' ? 'bg-blue-100 text-blue-800' : 
                                                       'bg-green-100 text-green-800') }}">
                                                    {{ $user->role }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Approval Rates -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Rates</h3>
                        <div class="space-y-4">
                            @foreach($approvalRates as $rate)
                                @if($rate['total_reviewed'] > 0)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $rate['user']->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $rate['total_reviewed'] }} reviewed</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-semibold 
                                                {{ $rate['approval_rate'] >= 80 ? 'text-green-600' : 
                                                   ($rate['approval_rate'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $rate['approval_rate'] }}%
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $rate['approved_count'] }} approved</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Insights -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Insights</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">Processing Time Analysis</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Average processing time:</span>
                                    <span class="font-medium">{{ $processingMetrics->avg_processing_time ? round($processingMetrics->avg_processing_time, 1) : 0 }} days</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Processing range:</span>
                                    <span class="font-medium">{{ $processingMetrics->min_processing_time ?? 0 }} - {{ $processingMetrics->max_processing_time ?? 0 }} days</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total processed:</span>
                                    <span class="font-medium">{{ $processingMetrics->total_processed ?? 0 }} DCRs</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">Compliance Metrics</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">On-time completion:</span>
                                    <span class="font-medium text-green-600">
                                        {{ $dueDateCompliance->total > 0 ? round(($dueDateCompliance->on_time / $dueDateCompliance->total) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Overdue rate:</span>
                                    <span class="font-medium text-red-600">
                                        {{ $dueDateCompliance->total > 0 ? round(($dueDateCompliance->overdue / $dueDateCompliance->total) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total compliance score:</span>
                                    <span class="font-medium text-blue-600">
                                        {{ $dueDateCompliance->total > 0 ? max(0, 100 - round(($dueDateCompliance->overdue / $dueDateCompliance->total) * 100)) : 100 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
