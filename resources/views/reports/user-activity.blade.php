<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Activity Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Report Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">User Activity Report</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ Carbon\Carbon::parse($startDate)->toFormattedDateString() }} - {{ Carbon\Carbon::parse($endDate)->toFormattedDateString() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Activity Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Activity Summary</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Activity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($userActivity as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $activity['user']->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $activity['user']->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $activity['user']->role === 'Admin' ? 'bg-purple-100 text-purple-800' : 
                                                   ($activity['user']->role === 'Author' ? 'bg-blue-100 text-blue-800' : 
                                                   'bg-green-100 text-green-800') }}">
                                                {{ $activity['user']->role }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $activity['submitted_count'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $activity['reviewed_count'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ ($activity['submitted_count'] + $activity['reviewed_count']) > 10 ? 'bg-green-100 text-green-800' : 
                                                   (($activity['submitted_count'] + $activity['reviewed_count']) > 5 ? 'bg-yellow-100 text-yellow-800' : 
                                                   'bg-gray-100 text-gray-800') }}">
                                                {{ $activity['submitted_count'] + $activity['reviewed_count'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($activity['last_activity'])
                                                {{ Carbon\Carbon::parse($activity['last_activity'])->toFormattedDateString() }}
                                            @else
                                                <span class="text-gray-400">No activity</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Daily Activity Trends -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Activity Trends</h3>
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach($dailyActivity as $activity)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ Carbon\Carbon::parse($activity->date)->toFormattedDateString() }}
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="bg-blue-500 h-2 rounded-full" 
                                                 style="width: {{ $dailyActivity->max('submitted_count') > 0 ? ($activity->submitted_count / $dailyActivity->max('submitted_count')) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-600 w-8 text-right">{{ $activity->submitted_count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Role Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Distribution</h3>
                        <div class="space-y-4">
                            @foreach($roleActivity as $role)
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium">{{ $role->role }}</span>
                                        <span class="text-sm text-gray-600">{{ $role->user_count }} users</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="h-3 rounded-full transition-all duration-300
                                            {{ $role->role === 'Admin' ? 'bg-purple-500' : 
                                               ($role->role === 'Author' ? 'bg-blue-500' : 
                                               'bg-green-500') }}"
                                             style="width: {{ $roleActivity->sum('user_count') > 0 ? ($role->user_count / $roleActivity->sum('user_count')) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Insights -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Insights</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">Most Active Users</h4>
                            <div class="space-y-2">
                                @php
                                    $mostActive = $userActivity->sortByDesc(function($activity) {
                                        return $activity['submitted_count'] + $activity['reviewed_count'];
                                    })->take(5);
                                @endphp
                                @foreach($mostActive as $activity)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">{{ $activity['user']->name }}</span>
                                        <span class="font-medium">{{ $activity['submitted_count'] + $activity['reviewed_count'] }} activities</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">Top Submitters</h4>
                            <div class="space-y-2">
                                @php
                                    $topSubmitters = $userActivity->sortByDesc('submitted_count')->take(5);
                                @endphp
                                @foreach($topSubmitters as $activity)
                                    @if($activity['submitted_count'] > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">{{ $activity['user']->name }}</span>
                                            <span class="font-medium">{{ $activity['submitted_count'] }} submitted</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">Top Reviewers</h4>
                            <div class="space-y-2">
                                @php
                                    $topReviewers = $userActivity->sortByDesc('reviewed_count')->take(5);
                                @endphp
                                @foreach($topReviewers as $activity)
                                    @if($activity['reviewed_count'] > 0)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">{{ $activity['user']->name }}</span>
                                            <span class="font-medium">{{ $activity['reviewed_count'] }} reviewed</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $userActivity->sum('submitted_count') }}
                            </div>
                            <div class="text-sm text-gray-600">Total DCRs Submitted</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $userActivity->sum('reviewed_count') }}
                            </div>
                            <div class="text-sm text-gray-600">Total DCRs Reviewed</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ $userActivity->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Active Users</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ $dailyActivity->sum('submitted_count') }}
                            </div>
                            <div class="text-sm text-gray-600">Daily Average</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
