<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendar - Planform DCR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50">
    <div class="flex h-screen overflow-hidden" x-data="calendarManager()">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('layouts.header')

            <!-- Calendar Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-8">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 animate-fade-in">
                    <!-- Calendar Grid -->
                    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <h2 class="text-xl font-bold text-navy-900">{{ $currentDate->format('F Y') }}</h2>
                                <div class="flex gap-1 bg-slate-50 p-1 rounded-lg border border-slate-200">
                                    <a href="{{ route('calendar.index', ['year' => $currentDate->copy()->subMonth()->year, 'month' => $currentDate->copy()->subMonth()->month]) }}" 
                                       class="p-1.5 hover:bg-white hover:shadow-sm rounded transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('calendar.index', ['year' => $currentDate->copy()->addMonth()->year, 'month' => $currentDate->copy()->addMonth()->month]) }}" 
                                       class="p-1.5 hover:bg-white hover:shadow-sm rounded transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('dcr.create') }}" class="px-4 py-2 bg-secondary text-white rounded-lg font-bold text-sm shadow-lg shadow-orange-500/20 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                New Event
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50/50">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $day }}</div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-7 h-[600px]">
                            @php
                                // Add empty cells for days before month starts
                                for ($i = 0; $i < $firstDayOfWeek; $i++) {
                                    echo '<div class="border-r border-b border-slate-50 p-2 bg-slate-50/30"></div>';
                                }
                                
                                // Generate calendar days
                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                    $dateStr = $currentDate->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
                                    $dayEvents = array_filter($calendarEvents, function($event) use ($dateStr) {
                                        return $event['date'] === $dateStr;
                                    });
                                    $isToday = $dateStr === now()->format('Y-m-d');
                                    
                                    echo '<div class="border-r border-b border-slate-50 p-2 relative group hover:bg-slate-50/50 transition-colors">';
                                    echo '<span class="text-sm font-bold ' . ($isToday ? 'bg-secondary text-white w-7 h-7 flex items-center justify-center rounded-full' : 'text-slate-700') . '">' . $day . '</span>';
                                    
                                    if (count($dayEvents) > 0) {
                                        echo '<div class="mt-2 space-y-1">';
                                        foreach ($dayEvents as $event) {
                                            $bgColor = $event['type'] === 'deadline' ? 'bg-red-50 text-red-600 border-red-100' : 
                                                      ($event['type'] === 'meeting' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-green-50 text-green-600 border-green-100');
                                            echo '<div class="text-[10px] p-1.5 rounded font-bold truncate border ' . $bgColor . '">' . htmlspecialchars($event['title']) . '</div>';
                                        }
                                        echo '</div>';
                                    }
                                    
                                    echo '</div>';
                                }
                            @endphp
                        </div>
                    </div>

                    <!-- Upcoming Events Sidebar -->
                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <h3 class="font-bold text-navy-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Upcoming
                            </h3>
                            <div class="space-y-4">
                                @forelse($upcomingEvents as $event)
                                <div class="flex gap-3 items-start group">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center shrink-0">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $event['month'] }}</span>
                                        <span class="text-sm font-bold text-navy-900">{{ $event['day'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-800 group-hover:text-secondary transition-colors cursor-pointer">
                                            {{ $event['title'] }}
                                        </h4>
                                        <div class="flex items-center gap-3 mt-1 text-[11px] text-slate-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $event['time'] }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $event['location'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="py-8 text-center text-slate-400">
                                    <p class="text-xs font-bold uppercase tracking-widest italic">No upcoming events</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-blue-800 p-6 rounded-2xl text-white shadow-xl shadow-blue-800/20">
                            <h3 class="font-bold mb-2">Efficiency Tip</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Upload your technical documentation at least 24 hours before the 'Conceptual Phase' review to ensure all stakeholders have sufficient time for initial checks.
                            </p>
                            <button class="mt-4 text-xs font-bold text-secondary flex items-center gap-1 hover:underline">
                                View Protocol 
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function calendarManager() {
            return {
                currentDate: new Date({{ $year }}, {{ $month - 1 }}, 1),
                
                init() {
                    console.log('Calendar initialized for', this.currentDate);
                }
            }
        }
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
    </style>
</body>
</html>
