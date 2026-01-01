<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.dashboard') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" 
                                   class="mt-1 block w-48 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" 
                                   class="mt-1 block w-48 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filter
                        </button>
                        <button type="button" onclick="resetFilters()" 
                                class="px-4 py-2 bg-gray-300 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Reset
                        </button>
                    </form>
                </div>
            </div>

            <!-- Report Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- DCR Summary Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.dcr.summary', ['start_date' => $startDate, 'end_date' => $endDate]) }}'">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m9 4v1a1 1 0 001 1h4a1 1 0 001-1v-1m-3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">DCR Summary Report</dt>
                                    <dd class="mt-1 text-sm text-gray-600">
                                        Overall statistics, trends, and metrics
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Comprehensive overview of all DCRs
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Impact Analysis Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.impact.analysis', ['start_date' => $startDate, 'end_date' => $endDate]) }}'">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Impact Analysis Report</dt>
                                    <dd class="mt-1 text-sm text-gray-600">
                                        Impact ratings and escalation analysis
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Risk assessment and escalation metrics
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.performance.metrics', ['start_date' => $startDate, 'end_date' => $endDate]) }}'">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Performance Metrics Report</dt>
                                    <dd class="mt-1 text-sm text-gray-600">
                                        User performance and processing times
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Efficiency and productivity analysis
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compliance Audit Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.compliance.audit', ['start_date' => $startDate, 'end_date' => $endDate]) }}'">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Compliance Audit Report</dt>
                                    <dd class="mt-1 text-sm text-gray-600">
                                        Compliance and audit trail analysis
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Regulatory compliance tracking
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Activity Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('reports.user.activity', ['start_date' => $startDate, 'end_date' => $endDate]) }}'">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">User Activity Report</dt>
                                    <dd class="mt-1 text-sm text-gray-600">
                                        User participation and activity trends
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                User engagement and productivity
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Export Reports</dt>
                                    <dd class="mt-1 text-sm text-gray-600">
                                        Download reports in CSV format
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <a href="{{ route('reports.export', ['type' => 'summary', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="block w-full text-left px-3 py-2 text-sm bg-gray-50 rounded hover:bg-gray-100">
                                📊 Export DCR Summary
                            </a>
                            <a href="{{ route('reports.export', ['type' => 'impact', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="block w-full text-left px-3 py-2 text-sm bg-gray-50 rounded hover:bg-gray-100">
                                ⚠️ Export Impact Analysis
                            </a>
                            <a href="{{ route('reports.export', ['type' => 'performance', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="block w-full text-left px-3 py-2 text-sm bg-gray-50 rounded hover:bg-gray-100">
                                📈 Export Performance Metrics
                            </a>
                            <a href="{{ route('reports.export', ['type' => 'compliance', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                               class="block w-full text-left px-3 py-2 text-sm bg-gray-50 rounded hover:bg-gray-100">
                                ✅ Export Compliance Audit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Overview -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ App\Models\Dcr::whereBetween('created_at', [$startDate, $endDate])->count() }}
                            </div>
                            <div class="text-sm text-gray-500">Total DCRs</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ App\Models\Dcr::whereBetween('created_at', [$startDate, $endDate])->where('status', 'Approved')->count() }}
                            </div>
                            <div class="text-sm text-gray-500">Approved</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ App\Models\Dcr::whereBetween('created_at', [$startDate, $endDate])->where('status', 'Pending')->count() }}
                            </div>
                            <div class="text-sm text-gray-500">Pending</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">
                                {{ App\Models\Dcr::whereBetween('created_at', [$startDate, $endDate])->where('impact_rating', 'High')->count() }}
                            </div>
                            <div class="text-sm text-gray-500">High Impact</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function resetFilters() {
            const today = new Date().toISOString().split('T')[0];
            const threeMonthsAgo = new Date();
            threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
            const startDate = threeMonthsAgo.toISOString().split('T')[0];
            
            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = today;
            
            window.location.href = '{{ route('reports.dashboard') }}';
        }
    </script>
</x-app-layout>
