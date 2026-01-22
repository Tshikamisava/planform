<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Impact Analysis Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Report Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Impact Analysis Report</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($startDate)->toFormattedDateString() }} - {{ Carbon\Carbon::parse($endDate)->toFormattedDateString() }}
                            </span>
                            <a href="{{ route('reports.export', ['type' => 'impact', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Impact Distribution Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Impact Rating Distribution</h3>
                        <div class="space-y-4">
                            @foreach($impactDistribution as $impact)
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium">{{ $impact->impact_rating ?? 'Not Rated' }}</span>
                                        <span class="text-sm text-gray-600">{{ $impact->count }} DCRs</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="h-3 rounded-full transition-all duration-300
                                            {{ $impact->impact_rating === 'High' ? 'bg-red-500' : 
                                               ($impact->impact_rating === 'Medium' ? 'bg-yellow-500' : 
                                               ($impact->impact_rating === 'Low' ? 'bg-green-500' : 'bg-gray-500')) }}"
                                             style="width: {{ $impactDistribution->sum('count') > 0 ? ($impact->count / $impactDistribution->sum('count')) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Impact vs Status Correlation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Impact vs Status Correlation</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Impact</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Approved</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rejected</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($impactStatusCorrelation->groupBy('impact_rating') as $impact => $statuses)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $impact === 'High' ? 'bg-red-100 text-red-800' : 
                                                       ($impact === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($impact === 'Low' ? 'bg-green-100 text-green-800' : 
                                                       'bg-gray-100 text-gray-800')) }}">
                                                    {{ $impact ?? 'Not Rated' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                {{ $statuses->where('status', 'Approved')->sum('count') }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                {{ $statuses->where('status', 'Rejected')->sum('count') }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                {{ $statuses->where('status', 'Pending')->sum('count') }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $statuses->sum('count') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Processing Time by Impact -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Average Processing Time by Impact</h3>
                        <div class="space-y-4">
                            @foreach($processingTimeByImpact as $time)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full mr-3 
                                            {{ $time->impact_rating === 'High' ? 'bg-red-500' : ($time->impact_rating === 'Medium' ? 'bg-yellow-500' : ($time->impact_rating === 'Low' ? 'bg-green-500' : 'bg-gray-500')) }}">
                                        </span>
                                        <span class="text-sm font-medium">{{ $time->impact_rating ?? 'Not Rated' }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-blue-600">
                                            {{ round($time->avg_days, 1) }} days
                                        </div>
                                        <div class="text-xs text-gray-500">Average</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Escalation Analysis -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Escalation Analysis</h3>
                        <div class="space-y-4">
                            @foreach($escalationAnalysis as $escalation)
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                        <span class="text-sm font-medium">{{ $escalation->impact_rating }} Impact</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-purple-600">
                                            {{ $escalation->count }}
                                        </div>
                                        <div class="text-xs text-gray-500">Escalated</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- High Impact DCRs Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">High Impact DCRs Details</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Escalated</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Escalated To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($highImpactDcrs as $dcr)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $dcr->dcr_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $dcr->request_type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $dcr->author->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $dcr->status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                                   ($dcr->status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                                   ($dcr->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-gray-100 text-gray-800')) }}">
                                                {{ $dcr->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($dcr->auto_escalated)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Yes
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    No
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $dcr->escalatedTo ? $dcr->escalatedTo->name : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $dcr->created_at->toFormattedDateString() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($highImpactDcrs->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No High Impact DCRs</h3>
                            <p class="mt-1 text-sm text-gray-500">No high impact DCRs found in the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
