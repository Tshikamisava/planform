<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('DCR Summary Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Report Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">DCR Summary Report</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($startDate)->toFormattedDateString() }} - {{ Carbon\Carbon::parse($endDate)->toFormattedDateString() }}
                            </span>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.export', ['type' => 'summary', 'format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                   class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    CSV
                                </a>
                                <a href="{{ route('reports.export', ['type' => 'summary', 'format' => 'excel', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                   class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Excel
                                </a>
                                <a href="{{ route('reports.export', ['type' => 'summary', 'format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                   class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $totalDcrs }}</div>
                            <div class="text-sm text-gray-600">Total DCRs</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ $approvedDcrs }}</div>
                            <div class="text-sm text-gray-600">Approved</div>
                            <div class="text-xs text-gray-500">{{ $totalDcrs > 0 ? round(($approvedDcrs / $totalDcrs) * 100, 1) : 0 }}%</div>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-indigo-600">{{ $completedDcrs }}</div>
                            <div class="text-sm text-gray-600">Completed</div>
                            <div class="text-xs text-gray-500">{{ $totalDcrs > 0 ? round(($completedDcrs / $totalDcrs) * 100, 1) : 0 }}%</div>
                        </div>
                        <div class="bg-black bg-opacity-5 rounded-lg p-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $closedDcrs }}</div>
                            <div class="text-sm text-gray-600">Closed</div>
                            <div class="text-xs text-gray-500">{{ $totalDcrs > 0 ? round(($closedDcrs / $totalDcrs) * 100, 1) : 0 }}%</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-red-600">{{ $rejectedDcrs }}</div>
                            <div class="text-sm text-gray-600">Rejected</div>
                            <div class="text-xs text-gray-500">{{ $totalDcrs > 0 ? round(($rejectedDcrs / $totalDcrs) * 100, 1) : 0 }}%</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-yellow-600">{{ $pendingDcrs }}</div>
                            <div class="text-sm text-gray-600">Pending</div>
                            <div class="text-xs text-gray-500">{{ $totalDcrs > 0 ? round(($pendingDcrs / $totalDcrs) * 100, 1) : 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Impact Rating Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Impact Rating Distribution</h3>
                        <div class="space-y-3">
                            @foreach($impactStats as $stat)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full mr-3 
                                            {{ $stat->impact_rating === 'High' ? 'bg-red-500' : 
                                               ($stat->impact_rating === 'Medium' ? 'bg-yellow-500' : 
                                               ($stat->impact_rating === 'Low' ? 'bg-green-500' : 'bg-gray-500')) }}">
                                        </span>
                                        <span class="text-sm font-medium">{{ $stat->impact_rating ?? 'Not Rated' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="h-2 rounded-full 
                                                {{ $stat->impact_rating === 'High' ? 'bg-red-500' : 
                                                   ($stat->impact_rating === 'Medium' ? 'bg-yellow-500' : 
                                                   ($stat->impact_rating === 'Low' ? 'bg-green-500' : 'bg-gray-500')) }}"
                                                 style="width: {{ $totalDcrs > 0 ? ($stat->count / $totalDcrs) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-600 w-12 text-right">{{ $stat->count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Processing Time Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Average Processing Time</span>
                                <span class="text-lg font-semibold text-blue-600">
                                    {{ $avgProcessingTime->avg_days ? round($avgProcessingTime->avg_days, 1) : 0 }} days
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Escalated DCRs</span>
                                <span class="text-lg font-semibold text-purple-600">{{ $escalatedDcrs }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Escalation Rate</span>
                                <span class="text-lg font-semibold text-purple-600">
                                    {{ $totalDcrs > 0 ? round(($escalatedDcrs / $totalDcrs) * 100, 1) : 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Request Types -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Request Types</h3>
                        <div class="space-y-2">
                            @foreach($topRequestTypes as $type)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-700">{{ $type->request_type }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $type->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Monthly Trends -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trends</h3>
                        <div class="space-y-2">
                            @foreach($monthlyTrends->groupBy('month') as $month => $trends)
                                <div class="border-b border-gray-100 pb-2 mb-2">
                                    <div class="text-sm font-medium text-gray-700 mb-1">
                                        {{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                                    </div>
                                    <div class="flex space-x-4 text-xs">
                                        @foreach($trends as $trend)
                                            <span class="px-2 py-1 rounded-full whitespace-nowrap mb-1
                                                {{ $trend->status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                                   ($trend->status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                                   ($trend->status === 'Completed' ? 'bg-blue-100 text-blue-800' : 
                                                   ($trend->status === 'Closed' ? 'bg-gray-800 text-white' : 
                                                   ($trend->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-gray-100 text-gray-800')))) }}">
                                                {{ $trend->status }}: {{ $trend->count }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed DCR Data</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DCR ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processing Days</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $dcrs = App\Models\Dcr::whereBetween('created_at', [$startDate, $endDate])
                                        ->with(['author', 'recipient'])
                                        ->orderBy('created_at', 'desc')
                                        ->take(50)
                                        ->get();
                                @endphp
                                @foreach($dcrs as $dcr)
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
                                                   ($dcr->status === 'Completed' ? 'bg-blue-100 text-blue-800' : 
                                                   ($dcr->status === 'Closed' ? 'bg-gray-800 text-white' : 
                                                   ($dcr->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-gray-100 text-gray-800')))) }}">
                                                {{ $dcr->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($dcr->impact_rating)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $dcr->impact_rating === 'High' ? 'bg-red-100 text-red-800' : 
                                                       ($dcr->impact_rating === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                                                       'bg-green-100 text-green-800') }}">
                                                    {{ $dcr->impact_rating }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">Not Rated</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $dcr->created_at->toFormattedDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if(in_array($dcr->status, ['Approved', 'Rejected', 'Completed', 'Closed']))
                                                {{ $dcr->updated_at->diffInDays($dcr->created_at) }}
                                            @else
                                                -
                                            @endif
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
