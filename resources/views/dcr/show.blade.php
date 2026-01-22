<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DCR {{ $dcr->dcr_id }} - Planform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50">
    <div class="flex h-screen overflow-hidden" x-data="dcrDetailManager()">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('layouts.header')

            <!-- DCR Detail Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-8">
                <div class="max-w-4xl mx-auto space-y-8 pb-20 animate-fade-in relative">
                    
                    <!-- Toast Notification -->
                    <div x-show="showToast" 
                         x-transition
                         :class="toastType === 'success' ? 'bg-green-600 border-green-700' : 'bg-red-600 border-red-700'"
                         class="fixed top-24 right-8 z-[250] text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 border">
                        <svg x-show="toastType === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="toastType === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-sm font-bold" x-text="toastMessage"></span>
                    </div>

                    <!-- Detail Header -->
                    <div class="flex items-center gap-4">
                        <a href="{{ route('dashboard') }}" class="p-3 text-slate-400 hover:text-navy-900 bg-white border border-slate-200 rounded-xl transition-all shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-4xl font-black text-slate-900">{{ $dcr->dcr_id }}</h1>
                                <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-blue-600 text-white uppercase tracking-wide">
                                    {{ $dcr->status }}
                                </span>
                            </div>
                            <p class="text-slate-500 font-semibold mt-1 uppercase tracking-wide text-xs">
                                {{ $dcr->title ?? $dcr->reason_for_change }}
                            </p>
                        </div>
                    </div>

                    <!-- Enhanced Phase Timeline with Progress Bar -->
                    <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ animateProgress: false }" x-init="setTimeout(() => animateProgress = true, 100)">
                        <!-- Progress Bar Background -->
                        <div class="relative mb-12">
                            <div class="absolute top-5 left-0 right-0 h-1 bg-slate-200 rounded-full"></div>
                            <!-- Animated Progress Fill -->
                            <div class="absolute top-5 left-0 h-1 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-full shadow-lg shadow-blue-500/50 transition-all duration-[1500ms] ease-out"
                                 :style="`width: ${animateProgress ? '{{ ($currentStepIndex / (count($workflowSteps) - 1)) * 100 }}%' : '0%'}`">
                                <!-- Glowing effect at the end -->
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-600 rounded-full animate-pulse shadow-lg shadow-blue-500/50"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between relative">
                            @foreach($workflowSteps as $index => $step)
                                @php
                                    $isCompleted = $index < $currentStepIndex;
                                    $isCurrent = $index === $currentStepIndex;
                                    $delay = $index * 100;
                                @endphp
                                <div class="flex flex-col items-center flex-1 relative z-10 transform transition-all duration-500"
                                     x-data="{ show: false }"
                                     x-init="setTimeout(() => show = true, {{ $delay }})"
                                     :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                                    
                                    <!-- Step Circle with Animation -->
                                    <div class="relative">
                                        @if($isCompleted || $isCurrent)
                                            <!-- Pulse ring for current/completed -->
                                            <div class="absolute inset-0 rounded-full bg-blue-600 animate-ping opacity-20"></div>
                                        @endif
                                        
                                        <div class="relative w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500 transform hover:scale-110
                                            {{ $isCompleted ? 'bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-500/50' : '' }}
                                            {{ $isCurrent ? 'bg-gradient-to-br from-blue-600 to-blue-800 text-white shadow-xl shadow-blue-600/60 ring-4 ring-blue-200' : '' }}
                                            {{ !$isCompleted && !$isCurrent ? 'bg-slate-100 text-slate-400' : '' }}">
                                            
                                            @if($isCompleted)
                                                <svg class="w-6 h-6 animate-[bounce_1s_ease-in-out]" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @elseif($isCurrent)
                                                <div class="relative">
                                                    <span class="relative z-10">{{ $index + 1 }}</span>
                                                    <div class="absolute inset-0 bg-white/20 rounded-full animate-pulse"></div>
                                                </div>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Step Label -->
                                    <span class="mt-4 text-[10px] font-bold text-center w-28 uppercase tracking-wider transition-all duration-500
                                        {{ $isCurrent ? 'text-blue-600 scale-105' : 'text-slate-400' }}
                                        {{ $isCompleted ? 'text-slate-600' : '' }}">
                                        {{ $step }}
                                    </span>
                                    
                                    <!-- Status Indicator -->
                                    @if($isCurrent)
                                        <div class="mt-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[8px] font-bold uppercase tracking-wide animate-pulse">
                                            In Progress
                                        </div>
                                    @elseif($isCompleted)
                                        <div class="mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-[8px] font-bold uppercase tracking-wide">
                                            Complete
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Progress Percentage -->
                        <div class="mt-8 text-center">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-full border border-blue-200">
                                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                                <span class="text-sm font-bold text-blue-900">
                                    {{ round(($currentStepIndex / (count($workflowSteps) - 1)) * 100) }}% Complete
                                </span>
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Manifest Description -->
                            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="p-3 bg-blue-800 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Manifest Description</h2>
                                </div>
                                <p class="text-slate-600 leading-relaxed text-sm bg-slate-50 p-6 rounded-2xl">
                                    {{ $dcr->reason_for_change }}
                                    
                                    @if($dcr->description)
                                    <br><br>{{ $dcr->description }}
                                    @endif
                                </p>
                            </div>

                            <!-- Discussion / Log -->
                            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                                <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                                    <div class="p-3 bg-blue-800 rounded-xl">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </div>
                                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Discussion / Log</h2>
                                </div>
                                <div class="p-6 space-y-4 max-h-[400px] overflow-y-auto">
                                    @forelse($activityLog as $log)
                                    <div class="flex gap-4 items-start">
                                        <div class="w-12 h-12 rounded-full bg-cyan-400 flex items-center justify-center text-white font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($log['user'], 0, 2)) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-semibold text-sm text-slate-900">{{ $log['user'] }}</span>
                                                <span class="text-xs text-slate-400 uppercase">{{ $log['timestamp'] }}</span>
                                            </div>
                                            <p class="text-sm text-slate-600">{{ $log['action'] }}</p>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="py-12 text-center text-slate-400">
                                        <p class="text-xs font-bold uppercase tracking-[0.2em] italic">No activity logged yet</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Authorization Audit Trail -->
                            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="p-3 bg-blue-800 rounded-xl">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Audit Trail</h2>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide px-3 py-1 bg-slate-100 rounded-full">ISO Verified</span>
                                </div>
                                <div class="p-6">
                                    @if($auditTrail->count() > 0)
                                    <div class="space-y-6">
                                        @foreach($auditTrail as $audit)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-full bg-teal-400 flex items-center justify-center text-white font-bold text-sm">
                                                    {{ strtoupper(substr($audit['user'], 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">{{ $audit['user'] }}</p>
                                                    <p class="text-xs font-semibold uppercase {{ $audit['type'] === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $audit['type'] === 'success' ? 'Technically Approved' : 'Rejected' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-semibold text-slate-900">{{ $audit['timestamp'] }}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="py-12 text-center text-slate-400 space-y-4 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                                        <svg class="w-12 h-12 mx-auto opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <p class="text-xs font-bold uppercase tracking-[0.2em] italic">No formal authorizations logged</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <!-- Approval Action -->
                            <div class="bg-blue-800 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
                                @if($canPerformAction)
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-bold mb-2">Authorization</h3>
                                    <p class="text-sm text-blue-200 mb-8">Verify all technical manifests before sign-off.</p>
                                    <button @click="openApproveModal" 
                                            class="w-full py-4 bg-orange-500 text-white rounded-xl font-bold uppercase tracking-wide text-sm hover:bg-orange-600 transition-all flex items-center justify-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Authorize & Advance
                                    </button>
                                        <button @click="openRejectModal" 
                                                class="w-full py-3 bg-blue-700 text-white rounded-xl font-bold uppercase tracking-wide text-sm hover:bg-blue-600 transition-all flex items-center justify-center gap-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Reject Request
                                        </button>
                                    </div>
                                </div>
                                @else
                                <div class="relative z-10 py-4 opacity-75">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="p-2 bg-white/5 rounded-xl border border-white/10">
                                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-black tracking-tight">Security Lock</h3>
                                    </div>
                                    <p class="text-xs text-slate-400 italic">
                                        Current station lacks clearance. Assigned: 
                                        <span class="text-secondary font-black">{{ $dcr->recipient->name ?? 'N/A' }}</span>.
                                    </p>
                                </div>
                                @endif
                            </div>

                            <!-- Engineering Manifest Attachments -->
                            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Engineering Manifest</h2>
                                    <button onclick="window.location='{{ route('dcr.show', $dcr->id) }}?download=all'" 
                                            class="text-xs font-semibold text-orange-500 hover:text-orange-600 flex items-center gap-2 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download All
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    @forelse($attachments as $index => $file)
                                    @php
                                        $fileName = is_array($file) ? ($file['original_name'] ?? $file['name'] ?? 'Document') : basename($file);
                                        $filePath = is_array($file) ? ($file['path'] ?? $file['file_path'] ?? '') : $file;
                                        // If filePath is still an array, try to get a string value
                                        if (is_array($filePath)) {
                                            $filePath = reset($filePath); // Get first element
                                        }
                                        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                        $isPdf = $extension === 'pdf';
                                    @endphp
                                    <div data-file-path="{{ $filePath }}" 
                                         data-file-name="{{ $fileName }}" 
                                         data-is-pdf="{{ $isPdf ? 'true' : 'false' }}"
                                         onclick="handleFileClick(this)" 
                                         class="flex items-center justify-between p-4 border border-slate-100 rounded-3xl {{ $isPdf ? 'cursor-pointer' : 'cursor-default' }} hover:border-secondary transition-all group">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <div class="p-3 rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-secondary group-hover:text-white shrink-0">
                                                @if($isPdf)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                @elseif(in_array($extension, ['dwg', 'dxf', 'step', 'stp']))
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-sm text-navy-900 block truncate">{{ $fileName }}</span>
                                                    @if($isPdf)
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-bold rounded-full uppercase">PDF</span>
                                                    @endif
                                                </div>
                                                @if(is_array($file) && isset($file['size']))
                                                    <span class="text-xs text-slate-400">{{ number_format($file['size'] / 1024, 2) }} KB</span>
                                                @endif
                                            </div>
                                        </div>
                                        <svg class="w-5 h-5 text-slate-200 group-hover:text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    @empty
                                    <p class="text-center text-slate-400 text-xs py-8 italic">No attachments</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Impact Profile -->
                            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wide mb-4">Impact Profile</h2>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="p-4 bg-slate-50 rounded-2xl">
                                        <span class="text-xs text-slate-500 uppercase font-semibold block mb-1">Unit Delta</span>
                                        <span class="text-2xl font-bold text-slate-900">R{{ number_format($impactCost, 2) }}</span>
                                    </div>
                                    <div class="p-4 rounded-2xl {{ $inventoryScrap ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                                        <span class="text-xs uppercase font-semibold block mb-1">Inventory</span>
                                        <span class="text-2xl font-bold">{{ $inventoryScrap ? 'SCRAP' : 'OK' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approve Modal -->
                    <div x-show="showApproveModal" 
                         x-transition
                         class="fixed inset-0 bg-navy-900/98 backdrop-blur-2xl z-[300] flex items-center justify-center p-4">
                        <div class="bg-white rounded-[3.5rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.6)] p-0 max-w-2xl w-full overflow-hidden border border-slate-200">
                            <div class="bg-blue-800 px-8 py-12 flex flex-col items-center text-center relative overflow-hidden">
                                <div class="p-6 bg-white/5 rounded-[2.5rem] border border-white/10 mb-6 backdrop-blur-xl">
                                    <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-black text-white tracking-tighter mb-1 uppercase">Digital Sign-Off</h3>
                            </div>

                            <div class="p-10 space-y-8">
                                <form @submit.prevent="submitApproval" class="space-y-6">
                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block text-left">Approval Comments (Optional)</label>
                                        <textarea 
                                            x-model="approvalComments"
                                            rows="4"
                                            placeholder="Add any comments or recommendations..."
                                            class="w-full p-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-600 focus:bg-white transition-all resize-none text-sm"
                                        ></textarea>
                                    </div>

                                    <!-- Error Message -->
                                    <div x-show="approvalError" class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <p class="text-sm text-red-600 font-medium" x-text="approvalError"></p>
                                    </div>

                                    <div class="flex gap-4">
                                        <button type="button" @click="closeApproveModal" class="flex-1 py-4 text-slate-400 font-black uppercase text-[10px] hover:bg-slate-50 rounded-2xl transition-all">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="isApproving" class="flex-1 py-4 bg-green-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-2xl transition-all disabled:opacity-50 hover:bg-green-700">
                                            <span x-show="!isApproving">✓ Approve DCR</span>
                                            <span x-show="isApproving">Processing...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div x-show="showRejectModal" 
                         x-transition
                         class="fixed inset-0 bg-navy-900/98 backdrop-blur-2xl z-[300] flex items-center justify-center p-4">
                        <div class="bg-white rounded-[3.5rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.6)] p-0 max-w-2xl w-full overflow-hidden border border-slate-200">
                            <div class="bg-red-600 px-8 py-12 flex flex-col items-center text-center">
                                <div class="p-6 bg-white/10 rounded-[2.5rem] border border-white/20 mb-6">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-black text-white tracking-tighter uppercase">Reject DCR</h3>
                            </div>

                            <form @submit.prevent="submitRejection" class="p-10 space-y-6">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block">Rejection Reason *</label>
                                    <textarea x-model="rejectionReason" required minlength="10" maxlength="1000" rows="5" 
                                              class="w-full p-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-red-500 transition-all resize-none text-sm"
                                              placeholder="Provide detailed reason for rejection (minimum 10 characters)..."></textarea>
                                    <p class="text-xs text-slate-400" x-show="rejectionReason.length > 0">
                                        <span x-text="rejectionReason.length"></span> / 1000 characters
                                    </p>
                                </div>

                                <!-- Error Message -->
                                <div x-show="rejectionError" class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                    <p class="text-sm text-red-600 font-medium" x-text="rejectionError"></p>
                                </div>

                                <div class="flex gap-4">
                                    <button type="button" @click="closeRejectModal" class="flex-1 py-4 text-slate-400 font-black uppercase text-[10px] hover:bg-slate-50 rounded-2xl transition-all">
                                        Cancel
                                    </button>
                                    <button type="submit" :disabled="isRejecting || rejectionReason.length < 10" class="flex-1 py-4 bg-red-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-2xl transition-all disabled:opacity-50 hover:bg-red-700">
                                        <span x-show="!isRejecting">✗ Reject DCR</span>
                                        <span x-show="isRejecting">Processing...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        function dcrDetailManager() {
            return {
                showApproveModal: false,
                showRejectModal: false,
                approvalComments: '',
                isApproving: false,
                approvalError: '',
                rejectionReason: '',
                isRejecting: false,
                rejectionError: '',
                showToast: false,
                toastMessage: '',
                toastType: 'success',

                openApproveModal() {
                    this.showApproveModal = true;
                    this.approvalComments = '';
                    this.approvalError = '';
                },

                closeApproveModal() {
                    this.showApproveModal = false;
                    this.approvalComments = '';
                    this.approvalError = '';
                },

                openRejectModal() {
                    this.showRejectModal = true;
                    this.rejectionReason = '';
                    this.rejectionError = '';
                },

                closeRejectModal() {
                    this.showRejectModal = false;
                    this.rejectionReason = '';
                    this.rejectionError = '';
                },

                async submitApproval() {
                    this.isApproving = true;
                    this.approvalError = '';

                    try {
                        const response = await fetch('{{ route("dcr.approve", $dcr->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                comments: this.approvalComments 
                            })
                        });

                        let data;
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            data = await response.json();
                        } else {
                            const text = await response.text();
                            console.error('Non-JSON response:', text);
                            throw new Error('Server returned non-JSON response');
                        }

                        if (response.ok && data.success) {
                            this.toastMessage = data.message || 'DCR approved successfully!';
                            this.toastType = 'success';
                            this.showToast = true;
                            this.closeApproveModal();
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            this.approvalError = data.message || 'Failed to approve DCR. Please try again.';
                            this.toastMessage = this.approvalError;
                            this.toastType = 'error';
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 4000);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.approvalError = error.message || 'An error occurred. Please try again.';
                        this.toastMessage = this.approvalError;
                        this.toastType = 'error';
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 4000);
                    } finally {
                        this.isApproving = false;
                    }
                },

                async submitRejection() {
                    if (!this.rejectionReason || this.rejectionReason.trim().length < 10) {
                        this.rejectionError = 'Please provide a reason for rejection (minimum 10 characters).';
                        return;
                    }

                    this.isRejecting = true;
                    this.rejectionError = '';

                    try {
                        const response = await fetch('{{ route("dcr.reject", $dcr->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                rejection_reason: this.rejectionReason
                            })
                        });

                        let data;
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            data = await response.json();
                        } else {
                            const text = await response.text();
                            console.error('Non-JSON response:', text);
                            throw new Error('Server returned non-JSON response');
                        }

                        if (response.ok && data.success) {
                            this.toastMessage = data.message || 'DCR rejected successfully';
                            this.toastType = 'success';
                            this.showToast = true;
                            this.closeRejectModal();
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            this.rejectionError = data.message || 'Failed to reject DCR. Please try again.';
                            this.toastMessage = this.rejectionError;
                            this.toastType = 'error';
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 4000);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.rejectionError = error.message || 'An error occurred. Please try again.';
                        this.toastMessage = this.rejectionError;
                        this.toastType = 'error';
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 4000);
                    } finally {
                        this.isRejecting = false;
                    }
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

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .animate-shake {
            animation: shake 0.3s ease-in-out;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* PDF Viewer Modal Styles */
        .pdf-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .pdf-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pdf-modal-content {
            width: 95%;
            height: 95%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
        }

        .pdf-modal-header {
            background: #1e293b;
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pdf-modal-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pdf-modal-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-center;
            transition: background 0.2s;
        }

        .pdf-modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .pdf-modal-body {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .pdf-modal-body iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>

    <!-- PDF Viewer Modal -->
    <div id="pdfModal" class="pdf-modal">
        <div class="pdf-modal-content">
            <div class="pdf-modal-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span id="pdfModalTitle">PDF Document</span>
                </h3>
                <button onclick="closePdfViewer()" class="pdf-modal-close" title="Close (ESC)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="pdf-modal-body">
                <iframe id="pdfIframe" src="" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function handleFileClick(element) {
            const isPdf = element.getAttribute('data-is-pdf') === 'true';
            if (isPdf) {
                const filePath = element.getAttribute('data-file-path');
                const fileName = element.getAttribute('data-file-name');
                openPdfViewer(filePath, fileName);
            }
        }

        function openPdfViewer(filePath, fileName) {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfIframe');
            const title = document.getElementById('pdfModalTitle');
            
            // Set the title
            title.textContent = fileName;
            
            // Build the PDF URL using the document serving route
            // Extract just the filename from the path
            const filename = filePath.split('/').pop();
            const dcrId = {{ $dcr->id }};
            const pdfUrl = `/dcr/${dcrId}/document/${filename}`;
            
            // Set iframe src to show PDF
            iframe.src = pdfUrl;
            
            // Show modal
            modal.classList.add('active');
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closePdfViewer() {
            const modal = document.getElementById('pdfModal');
            const iframe = document.getElementById('pdfIframe');
            
            // Hide modal
            modal.classList.remove('active');
            
            // Clear iframe
            iframe.src = '';
            
            // Restore body scroll
            document.body.style.overflow = '';
        }

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePdfViewer();
            }
        });

        // Close on backdrop click
        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePdfViewer();
            }
        });
    </script>
