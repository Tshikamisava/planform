@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8 animate-fade-in"
                    
                    <!-- KPI Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($kpis as $kpi)
                        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-slate-500 text-sm font-medium">{{ $kpi['name'] }}</h3>
                                <span class="flex items-center text-xs font-semibold {{ $kpi['trend'] === 'up' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $kpi['change'] }}
                                    @if($kpi['trend'] === 'up')
                                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="text-3xl font-bold text-slate-900">
                                {{ $kpi['value'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <!-- Main Action Items -->
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        My Action Items
                                    </h2>
                                    <a href="{{ route('dcr.create') }}" class="text-sm text-secondary hover:text-orange-700 font-medium">
                                        + Create New DCR
                                    </a>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="text-xs text-white uppercase bg-blue-600 border-b border-blue-700">
                                            <tr>
                                                <th class="px-6 py-4 font-medium">Date</th>
                                                <th class="px-6 py-4 font-medium">Requestor / Author</th>
                                                <th class="px-6 py-4 font-medium">Entry by / Recipient</th>
                                                <th class="px-6 py-4 font-medium">Request</th>
                                                <th class="px-6 py-4 font-medium">Reason</th>
                                                <th class="px-6 py-4 font-medium">Description*</th>
                                                <th class="px-6 py-4 font-medium">Change Impact Rating</th>
                                                <th class="px-6 py-4 font-medium">Status</th>
                                                <th class="px-6 py-4 font-medium text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @forelse($actionItems as $dcr)
                                            <tr onclick="window.location='{{ route('dcr.show', $dcr->id) }}'" class="hover:bg-slate-50 cursor-pointer transition-colors group">
                                                <td class="px-6 py-4 text-slate-700">
                                                    {{ \Carbon\Carbon::parse($dcr->created_at)->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 font-medium text-slate-900">
                                                    {{ $dcr->author->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 text-slate-500">
                                                    {{ $dcr->recipient->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 font-semibold text-slate-700">
                                                    {{ $dcr->dcr_id }}
                                                </td>
                                                <td class="px-6 py-4 text-slate-600">
                                                    {{ Str::limit($dcr->reason_for_change, 30) }}
                                                </td>
                                                <td class="px-6 py-4 text-slate-600">
                                                    {{ Str::limit($dcr->description, 40) }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border
                                                        {{ $dcr->impact_rating === 'High' ? 'bg-red-50 text-red-600 border-red-200' : 
                                                           ($dcr->impact_rating === 'Medium' ? 'bg-yellow-50 text-yellow-600 border-yellow-200' : 
                                                           'bg-green-50 text-green-600 border-green-200') }}">
                                                        {{ $dcr->impact_rating ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border
                                                        {{ $dcr->status === 'Approved' ? 'bg-green-50 text-green-600 border-green-200' : 
                                                           ($dcr->status === 'pending' ? 'bg-blue-50 text-blue-600 border-blue-200' : 
                                                           ($dcr->status === 'Rejected' ? 'bg-red-50 text-red-600 border-red-200' : 
                                                           'bg-slate-100 text-slate-600 border-slate-200')) }}">
                                                        {{ ucfirst($dcr->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                                    <div class="relative" x-data="{ open: false }">
                                                        <button @click="open = !open" class="text-slate-400 group-hover:text-blue-600 transition-colors hover:bg-slate-100 p-1.5 rounded-lg">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                            </svg>
                                                        </button>
                                                        
                                                        <div x-show="open" 
                                                             @click.away="open = false"
                                                             x-transition:enter="transition ease-out duration-100"
                                                             x-transition:enter-start="opacity-0 scale-95"
                                                             x-transition:enter-end="opacity-100 scale-100"
                                                             x-transition:leave="transition ease-in duration-75"
                                                             x-transition:leave-start="opacity-100 scale-100"
                                                             x-transition:leave-end="opacity-0 scale-95"
                                                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50"
                                                             style="display: none;">
                                                            
                                                            <a href="{{ route('dcr.show', $dcr->id) }}" 
                                                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                                View
                                                            </a>
                                                            
                                                            @admin
                                                            <a href="{{ route('dcr.edit', $dcr->id) }}" 
                                                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                                Edit
                                                            </a>
                                                            
                                                            <form id="delete-form-{{ $dcr->id }}" action="{{ route('dcr.destroy', $dcr->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" 
                                                                        onclick="event.stopPropagation(); if(confirm('Are you sure you want to delete this DCR? This action cannot be undone.')) { document.getElementById('delete-form-{{ $dcr->id }}').submit(); }"
                                                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                    </svg>
                                                                    Delete
                                                                </button>
                                                            </form>
                                                            @endadmin
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="px-6 py-8 text-center text-slate-500">
                                                    No action items at the moment
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($actionItems->count() >= 5)
                                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                                    <a href="{{ route('dcr.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        View all action items →
                                    </a>
                                </div>
                                @endif
                            </div>

                             <!-- Pipeline Chart -->
                             <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                                <h2 class="text-lg font-bold text-slate-800 mb-6">Pipeline Overview</h2>
                                <div class="h-64 w-full">
                                    <canvas id="pipelineChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Feed & Quick Stats -->
                        <div class="space-y-6">
                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                                <h2 class="text-lg font-bold text-slate-800 mb-4">Recent Activity</h2>
                                <div class="space-y-6 relative">
                                    <div class="absolute left-3.5 top-2 h-full w-0.5 bg-slate-100"></div>
                                    @foreach($recentActivity as $activity)
                                    <div class="relative flex gap-4">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 z-10 border-2 border-white
                                            {{ $activity['type'] === 'success' ? 'bg-green-100 text-green-600' : 
                                               ($activity['type'] === 'error' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600') }}">
                                            @if($activity['type'] === 'success')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @elseif($activity['type'] === 'error')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-800">
                                                <span class="font-semibold">{{ $activity['user'] }}</span> {{ $activity['action'] }}
                                            </p>
                                            <p class="text-xs text-slate-400 mt-1">{{ $activity['timestamp'] }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-lg font-semibold mb-2">Need Help?</h3>
                                    <p class="text-white text-sm mb-4">
                                        Review the latest ISO-9001 compliance guidelines before submitting complex changes.
                                    </p>
                                    <button class="bg-white/10 hover:bg-white/20 text-white text-sm px-4 py-2 rounded-lg transition-colors border border-white/10">
                                        View Guidelines
                                    </button>
                                </div>
                                <div class="absolute -right-6 -bottom-6 opacity-10">
                                     <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('pipelineChart').getContext('2d');
        const chartData = @json($chartData);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.map(d => d.name),
                    datasets: [{
                        label: 'DCR Count',
                        data: chartData.map(d => d.count),
                        backgroundColor: ['#94a3b8', '#3b82f6', '#a855f7', '#22c55e'],
                        borderRadius: 4,
                        barThickness: 40,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            borderRadius: 8,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            border: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                color: '#f1f5f9'
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });

        // Delete confirmation function
        function confirmDelete(dcrId) {
            console.log('Delete function called for DCR ID:', dcrId);
            
            if (confirm('Are you sure you want to delete this DCR? This action cannot be undone.')) {
                console.log('User confirmed deletion');
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dcr/${dcrId}`;
                form.style.display = 'none';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found!');
                    alert('Error: CSRF token not found. Please refresh the page.');
                    return;
                }
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = csrfToken.getAttribute('content');
                
                form.appendChild(methodField);
                form.appendChild(csrfField);
                document.body.appendChild(form);
                
                console.log('Submitting delete form:', form.action);
                form.submit();
            } else {
                console.log('User cancelled deletion');
            }
        }
</script>
@endpush

@push('styles')
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
@endpush
