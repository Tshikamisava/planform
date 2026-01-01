<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Compliance Audit Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Report Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Compliance Audit Report</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($startDate)->toFormattedDateString() }} - {{ Carbon\Carbon::parse($endDate)->toFormattedDateString() }}
                            </span>
                            <a href="{{ route('reports.export', ['type' => 'compliance', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Missing Impact Ratings</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $missingImpactRatings->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Overdue DCRs</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $overdueDcrs->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Escalation Compliance</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $escalationCompliance->total_high_impact > 0 ? round(($escalationCompliance->escalated_count / $escalationCompliance->total_high_impact) * 100, 1) : 100 }}%
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Recommendation Compliance</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $recommendationCompliance->total_approved > 0 ? round(($recommendationCompliance->with_recommendations / $recommendationCompliance->total_approved) * 100, 1) : 100 }}%
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Missing Impact Ratings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Missing Impact Ratings</h3>
                        @if($missingImpactRatings->count() > 0)
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($missingImpactRatings as $dcr)
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $dcr->dcr_id }}</div>
                                            <div class="text-xs text-gray-500">{{ $dcr->author->name }} → {{ $dcr->recipient->name }}</div>
                                        </div>
                                        <a href="{{ route('dcr.impact.rating', $dcr) }}" 
                                           class="px-3 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                            Rate Now
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">All DCRs Rated</h3>
                                <p class="mt-1 text-sm text-gray-500">All pending DCRs have impact ratings.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Overdue DCRs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Overdue DCRs</h3>
                        @if($overdueDcrs->count() > 0)
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($overdueDcrs as $dcr)
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $dcr->dcr_id }}</div>
                                            <div class="text-xs text-gray-500">Due: {{ $dcr->due_date->toFormattedDateString() }}</div>
                                            <div class="text-xs text-gray-500">{{ $dcr->author->name }} → {{ $dcr->recipient->name }}</div>
                                        </div>
                                        <span class="px-2 py-1 bg-red-600 text-white text-xs rounded">
                                            {{ $dcr->due_date->diffInDays(now()) }} days overdue
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No Overdue DCRs</h3>
                                <p class="mt-1 text-sm text-gray-500">All DCRs are within their due dates.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Escalation Compliance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Escalation Compliance</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">High Impact DCRs</div>
                                    <div class="text-xs text-gray-500">Total high impact DCRs requiring escalation</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-semibold text-purple-600">{{ $escalationCompliance->total_high_impact ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Successfully Escalated</div>
                                    <div class="text-xs text-gray-500">DCRs that were properly escalated</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-semibold text-green-600">{{ $escalationCompliance->escalated_count ?? 0 }}</div>
                                </div>
                            </div>
                            @if($escalationCompliance->not_escalated_count > 0)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Not Escalated</div>
                                        <div class="text-xs text-gray-500">High impact DCRs missing escalation</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-red-600">{{ $escalationCompliance->not_escalated_count }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recommendation Compliance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommendation Compliance</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Total Approved DCRs</div>
                                    <div class="text-xs text-gray-500">All approved DCRs in the period</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-semibold text-blue-600">{{ $recommendationCompliance->total_approved ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">With Recommendations</div>
                                    <div class="text-xs text-gray-500">Approved DCRs that include recommendations</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-semibold text-green-600">{{ $recommendationCompliance->with_recommendations ?? 0 }}</div>
                                </div>
                            </div>
                            @if($recommendationCompliance->without_recommendations > 0)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Without Recommendations</div>
                                        <div class="text-xs text-gray-500">Approved DCRs missing recommendations</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-yellow-600">{{ $recommendationCompliance->without_recommendations }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity (Last 50)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentActivity as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $activity->dcr_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $activity->status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                                   ($activity->status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                                   ($activity->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-gray-100 text-gray-800')) }}">
                                                {{ $activity->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->author->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($activity->impact_rating)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $activity->impact_rating === 'High' ? 'bg-red-100 text-red-800' : 
                                                       ($activity->impact_rating === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                                                       'bg-green-100 text-green-800') }}">
                                                    {{ $activity->impact_rating }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">Not Rated</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->updated_at->toFormattedDateString() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
