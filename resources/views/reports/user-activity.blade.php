<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Activity Report') }}
            </h2>
            <div class="text-sm text-gray-500">
                Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('reports.dashboard', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Reports
                </a>
            </div>

            <!-- Activity Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @foreach($roleActivity as $role)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-{{ $role->role == 'Admin' ? 'purple' : ($role->role == 'DOM' ? 'indigo' : 'blue') }}-500">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ $role->role }}s</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900">{{ $role->user_count }}</div>
                            <div class="text-xs text-gray-400 mt-1">Total registered in system</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- User Activity Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed User Activity</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed/Assigned</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engagement</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($userActivity as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $activity['user']->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity['user']->role }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $activity['submitted_count'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $activity['reviewed_count'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity['last_activity'] ? \Carbon\Carbon::parse($activity['last_activity'])->diffForHumans() : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $total = $activity['submitted_count'] + $activity['reviewed_count'];
                                                $width = min(100, $total * 10);
                                            @endphp
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $width }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Trend Chart Placeholder (Visual Component) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Submission Trends</h3>
                    <div class="h-64 flex items-end justify-between space-x-2 px-4">
                        @foreach($dailyActivity as $trend)
                            @php
                                $maxVal = $dailyActivity->max('submitted_count') ?: 1;
                                $height = ($trend->submitted_count / $maxVal) * 100;
                            @endphp
                            <div class="flex-1 group relative">
                                <div class="bg-blue-200 group-hover:bg-blue-400 transition-colors w-full rounded-t" style="height: {{ max(10, $height) }}%">
                                    <div class="opacity-0 group-hover:opacity-100 absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-2 py-1 rounded text-xs whitespace-nowrap">
                                        {{ $trend->submitted_count }} DCRs on {{ $trend->date }}
                                    </div>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-2 truncate -rotate-45 origin-top-left">{{ \Carbon\Carbon::parse($trend->date)->format('M d') }}</div>
                            </div>
                        @endforeach
                        @if($dailyActivity->isEmpty())
                            <div class="flex-1 text-center py-20 text-gray-400">No activity data found for this period.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
