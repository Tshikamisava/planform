@extends('layouts.app')

@section('title', 'My Tasks - Workflow')

@section('content')
<div class="space-y-8 animate-fade-in pb-24 relative" x-data="workflowManager()">
    <!-- Toast Notification -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed top-24 right-8 z-[60] bg-blue-800 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 border border-blue-900"
         style="display: none;">
        <div class="bg-green-500 rounded-full p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <span class="text-sm font-bold tracking-tight" x-text="toastMessage"></span>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-navy-900">My Tasks</h1>
            <p class="text-slate-500 mt-1">Manage your pending approvals and active change requests.</p>
        </div>
        
        <div class="flex gap-4">
            <div class="bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="bg-orange-100 p-1.5 rounded text-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase font-bold">Pending</p>
                    <p class="text-lg font-bold text-navy-900">{{ $tasks->whereIn('status', ['Pending', 'In_Review'])->count() }}</p>
                </div>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="bg-green-100 p-1.5 rounded text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase font-bold">Completed</p>
                    <p class="text-lg font-bold text-navy-900">{{ $tasks->whereIn('status', ['Approved', 'Completed'])->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Task List Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden min-h-[500px] flex flex-col">
        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <svg class="w-[18px] h-[18px] text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span class="text-sm font-semibold text-slate-700">Filter:</span>
                <div class="flex bg-white rounded-lg border border-slate-200 p-1 ml-2">
                    <button @click="activeFilter = 'All'" 
                            :class="activeFilter === 'All' ? 'bg-blue-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'"
                            class="px-3 py-1 rounded text-xs font-medium transition-all">
                        All
                    </button>
                    <button @click="activeFilter = 'Pending'" 
                            :class="activeFilter === 'Pending' ? 'bg-blue-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'"
                            class="px-3 py-1 rounded text-xs font-medium transition-all">
                        Pending
                    </button>
                    <button @click="activeFilter = 'In_Review'" 
                            :class="activeFilter === 'In_Review' ? 'bg-blue-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'"
                            class="px-3 py-1 rounded text-xs font-medium transition-all">
                        In Review
                    </button>
                    <button @click="activeFilter = 'Approved'" 
                            :class="activeFilter === 'Approved' ? 'bg-blue-800 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800'"
                            class="px-3 py-1 rounded text-xs font-medium transition-all">
                        Approved
                    </button>
                </div>
            </div>
            <span x-show="selectedIds.length > 0" 
                  x-transition
                  class="text-xs font-bold text-secondary bg-orange-50 px-3 py-1 rounded-full border border-orange-100 animate-pulse">
                <span x-text="selectedIds.length"></span> Items Selected
            </span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 w-10">
                            <button @click="toggleSelectAll()" class="text-slate-400 hover:text-secondary transition-colors">
                                <svg x-show="selectedIds.length === filteredTasks.length && filteredTasks.length > 0" class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <svg x-show="!(selectedIds.length === filteredTasks.length && filteredTasks.length > 0)" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"></rect>
                                </svg>
                            </button>
                        </th>
                        <th class="px-6 py-4 w-24">ID</th>
                        <th class="px-6 py-4">Task Details</th>
                        <th class="px-6 py-4">Workflow Stage</th>
                        <th class="px-6 py-4">Priority</th>
                        <th class="px-6 py-4">Deadline</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tasks as $task)
                        <tr 
                            onclick="window.location.href='{{ route('dcr.show', $task->id) }}'"
                            :class="selectedIds.includes({{ $task->id }}) ? 'bg-orange-50/30' : ''"
                            x-show="activeFilter === 'All' || activeFilter === '{{ $task->status }}'"
                            class="hover:bg-slate-50 cursor-pointer group transition-colors"
                        >
                            <td class="px-6 py-4">
                                <button 
                                    @click.stop="toggleSelect({{ $task->id }})" 
                                    :class="selectedIds.includes({{ $task->id }}) ? 'text-secondary' : 'text-slate-300 group-hover:text-slate-400'"
                                    class="transition-colors"
                                >
                                    <svg x-show="selectedIds.includes({{ $task->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <svg x-show="!selectedIds.includes({{ $task->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"></rect>
                                    </svg>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">{{ $task->dcr_id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-navy-900 text-base">{{ $task->title }}</div>
                                <div class="text-xs text-slate-500 mt-1 flex gap-2">
                                    <span>Author: {{ $task->author->name }}</span>
                                    @if($task->recipient)
                                        <span>•</span>
                                        <span>Assigned: {{ $task->recipient->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Approved' => 'bg-green-50 text-green-700 border-green-200',
                                        'Completed' => 'bg-green-50 text-green-700 border-green-200',
                                        'In_Review' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Pending' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Rejected' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                    $dotColors = [
                                        'Approved' => 'bg-green-500',
                                        'Completed' => 'bg-green-500',
                                        'In_Review' => 'bg-blue-500',
                                        'Pending' => 'bg-purple-500',
                                        'Rejected' => 'bg-red-500',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColors[$task->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    <div class="w-1.5 h-1.5 rounded-full mr-2 {{ $dotColors[$task->status] ?? 'bg-slate-400' }}"></div>
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $priorityClass = '';
                                    $priorityIcon = '';
                                    switch($task->priority) {
                                        case 'Critical':
                                        case 'High':
                                            $priorityClass = 'text-red-600 font-bold text-xs bg-red-50 px-2 py-1 rounded border border-red-100';
                                            $priorityIcon = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                            break;
                                        case 'Medium':
                                            $priorityClass = 'text-orange-600 font-bold text-xs bg-orange-50 px-2 py-1 rounded border border-orange-100';
                                            $priorityIcon = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                            break;
                                        default:
                                            $priorityClass = 'text-slate-500 text-xs bg-slate-100 px-2 py-1 rounded border border-slate-200';
                                            $priorityIcon = '';
                                    }
                                @endphp
                                <span class="flex items-center gap-1.5 {{ $priorityClass }}">
                                    @if($priorityIcon)
                                        {!! $priorityIcon !!}
                                    @endif
                                    {{ $task->priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="{{ $task->due_date && $task->due_date->isToday() ? 'text-orange-600 font-bold' : '' }}">
                                        {{ $task->due_date ? ($task->due_date->isToday() ? 'Today' : $task->due_date->format('Y-m-d')) : 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="p-2 bg-white border border-slate-200 rounded-full text-slate-400 group-hover:text-secondary group-hover:border-secondary transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <div class="bg-slate-50 p-4 rounded-full mb-3">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                        </svg>
                                    </div>
                                    <p class="font-medium">No tasks found matching this filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Bulk Actions Bar -->
    <div x-show="selectedIds.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 w-full max-w-2xl px-4"
         style="display: none;">
        <div class="bg-blue-800 text-white rounded-2xl shadow-2xl shadow-blue-800/40 p-4 border border-blue-900 flex items-center justify-between">
            <div class="flex items-center gap-4 pl-2">
                <div class="bg-secondary text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm" x-text="selectedIds.length">
                </div>
                <div>
                    <h4 class="font-bold text-sm">Bulk Actions Available</h4>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">Perform phase transitions</p>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button 
                    @click="handleBulkAction('reject')"
                    :disabled="isProcessing"
                    :class="isProcessing ? 'opacity-50 cursor-not-allowed' : ''"
                    class="px-4 py-2 text-slate-400 hover:text-red-400 transition-colors flex items-center gap-2 text-sm font-bold"
                >
                    <svg x-show="isProcessing === 'reject'" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg x-show="isProcessing !== 'reject'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reject
                </button>
                <button 
                    @click="handleBulkAction('approve')"
                    :disabled="isProcessing"
                    :class="isProcessing ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 active:scale-95'"
                    class="px-6 py-2 bg-secondary text-white rounded-xl font-bold shadow-lg shadow-orange-500/20 transition-all flex items-center gap-2 text-sm"
                >
                    <svg x-show="isProcessing === 'approve'" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg x-show="isProcessing !== 'approve'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Approve Selected
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function workflowManager() {
    return {
        activeFilter: 'All',
        selectedIds: [],
        isProcessing: null,
        showToast: false,
        toastMessage: '',
        filteredTasks: [],

        init() {
            this.updateFilteredTasks();
        },

        updateFilteredTasks() {
            const allRows = document.querySelectorAll('tbody tr[x-show]');
            this.filteredTasks = Array.from(allRows).filter(row => {
                return this.activeFilter === 'All' || row.getAttribute('x-show').includes(this.activeFilter);
            });
        },

        toggleSelectAll() {
            const visibleRows = Array.from(document.querySelectorAll('tbody tr[x-show]')).filter(row => {
                const style = window.getComputedStyle(row);
                return style.display !== 'none';
            });

            if (this.selectedIds.length === visibleRows.length && visibleRows.length > 0) {
                this.selectedIds = [];
            } else {
                this.selectedIds = visibleRows.map(row => {
                    const checkbox = row.querySelector('button[\\@click\\.stop]');
                    return parseInt(checkbox.getAttribute('@click.stop').match(/\d+/)[0]);
                });
            }
        },

        toggleSelect(id) {
            const index = this.selectedIds.indexOf(id);
            if (index > -1) {
                this.selectedIds.splice(index, 1);
            } else {
                this.selectedIds.push(id);
            }
        },

        async handleBulkAction(type) {
            this.isProcessing = type;
            
            try {
                const response = await fetch(`/dcr/bulk-${type}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: this.selectedIds })
                });

                if (response.ok) {
                    this.toastMessage = `Successfully ${type === 'approve' ? 'approved' : 'rejected'} ${this.selectedIds.length} tasks.`;
                    this.showToast = true;
                    this.selectedIds = [];
                    
                    setTimeout(() => {
                        this.showToast = false;
                        window.location.reload();
                    }, 2000);
                }
            } catch (error) {
                console.error('Bulk action error:', error);
            } finally {
                this.isProcessing = null;
            }
        }
    };
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
