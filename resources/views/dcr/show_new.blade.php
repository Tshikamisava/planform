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
                         class="fixed top-24 right-8 z-[250] bg-blue-800 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 border border-blue-900">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-bold" x-text="toastMessage"></span>
                    </div>

                    <!-- Detail Header -->
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-6">
                            <a href="{{ route('dashboard') }}" class="p-2 text-slate-400 hover:text-navy-900 bg-white border border-slate-200 rounded-xl transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </a>
                            <div>
                                <div class="flex items-center gap-4">
                                    <h1 class="text-3xl font-black text-navy-900 tracking-tighter">{{ $dcr->dcr_id }}</h1>
                                    <span class="px-4 py-1 rounded-full text-[10px] font-black border bg-orange-100 text-secondary border-orange-200 uppercase tracking-[0.2em]">
                                        {{ $dcr->status }}
                                    </span>
                                </div>
                                <p class="text-slate-500 font-bold mt-1 uppercase tracking-widest text-[11px]">
                                    {{ $dcr->reason_for_change ?? 'DCR Manifest' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Phase Timeline -->
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm relative overflow-hidden group">
                        <div class="flex items-center justify-between relative z-10">
                            @foreach($workflowSteps as $index => $step)
                                @php
                                    $isCompleted = $index < $currentStepIndex;
                                    $isCurrent = $index === $currentStepIndex;
                                @endphp
                                <div class="flex flex-col items-center flex-1 relative">
                                    @if($index !== 0)
                                        <div class="absolute top-5 right-1/2 w-full h-1 -z-10 transition-all duration-700 {{ $index <= $currentStepIndex ? 'bg-secondary' : 'bg-slate-100' }}"></div>
                                    @endif
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-500 
                                        {{ $isCompleted || $isCurrent ? 'bg-secondary text-white shadow-xl shadow-orange-500/20' : 'bg-slate-100 text-slate-400' }}
                                        {{ $isCurrent ? 'ring-8 ring-orange-100 scale-110' : '' }}">
                                        @if($isCompleted)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <span class="mt-6 text-[9px] font-black text-center w-28 uppercase tracking-[0.1em] transition-colors duration-300 {{ $isCurrent ? 'text-secondary' : 'text-slate-400' }}">
                                        {{ $step }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Manifest Description -->
                            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8 space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-800 text-white rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h2 class="text-xl font-black text-navy-900 tracking-tight uppercase">Manifest Description</h2>
                                </div>
                                <p class="text-slate-600 leading-relaxed text-sm font-medium whitespace-pre-wrap bg-slate-50 p-6 rounded-3xl border border-slate-100">
                                    {{ $dcr->reason_for_change }}
                                    
                                    @if($dcr->description)
                                    <br><br>{{ $dcr->description }}
                                    @endif
                                </p>
                            </div>

                            <!-- Discussion / Log -->
                            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                                <div class="p-8 border-b border-slate-100 flex items-center gap-3 bg-slate-50/30">
                                    <div class="p-2 bg-blue-800 text-white rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </div>
                                    <h2 class="text-xl font-black text-navy-900 tracking-tight uppercase">Discussion / Log</h2>
                                </div>
                                <div class="p-8 space-y-8 max-h-[400px] overflow-y-auto custom-scrollbar">
                                    @forelse($activityLog as $log)
                                    <div class="flex gap-5 animate-fade-in group">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log['user']) }}&background=random&color=fff&bold=true" 
                                             class="w-11 h-11 rounded-[1.2rem] shadow-md border-2 border-white" alt="">
                                        <div class="flex-1 min-w-0">
                                            <div class="bg-slate-50 p-6 rounded-[2rem] rounded-tl-none border border-slate-100 shadow-sm {{ $log['type'] === 'error' ? 'border-red-100 bg-red-50/30' : '' }}">
                                                <div class="flex justify-between items-start mb-3">
                                                    <span class="font-black text-xs tracking-tight {{ $log['type'] === 'error' ? 'text-red-600' : 'text-navy-900' }}">
                                                        {{ $log['user'] }}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                                        {{ $log['timestamp'] }}
                                                    </span>
                                                </div>
                                                <p class="text-xs leading-relaxed font-medium {{ $log['type'] === 'error' ? 'text-red-700' : 'text-slate-600' }}">
                                                    {{ $log['action'] }}
                                                </p>
                                            </div>
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
                            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                                <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-800 text-white rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h2 class="text-xl font-black text-navy-900 tracking-tight uppercase">Audit Trail</h2>
                                    </div>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest border border-slate-200 px-3 py-1 rounded-full">ISO Verified</span>
                                </div>
                                <div class="p-8">
                                    @if($auditTrail->count() > 0)
                                    <div class="space-y-6">
                                        @foreach($auditTrail as $audit)
                                        <div class="flex items-center justify-between p-5 bg-slate-50 border border-slate-100 rounded-3xl group hover:bg-white hover:shadow-lg transition-all">
                                            <div class="flex items-center gap-4">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($audit['user']) }}&background=random&color=fff&bold=true" 
                                                     class="w-12 h-12 rounded-2xl shadow-sm border-2 border-white" alt="">
                                                <div>
                                                    <p class="text-sm font-black text-navy-900 leading-tight">{{ $audit['user'] }}</p>
                                                    <p class="text-[10px] font-bold uppercase tracking-widest mt-0.5 {{ $audit['type'] === 'success' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $audit['type'] === 'success' ? 'Technically Approved' : 'Rejected' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-[11px] font-black text-navy-900 tracking-tight">{{ $audit['timestamp'] }}</div>
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
                            <div class="bg-blue-800 rounded-3xl shadow-2xl p-8 text-white relative group overflow-hidden">
                                <div class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 transition-transform duration-1000">
                                    <svg class="w-40 h-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                @if($canPerformAction)
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-black mb-3 tracking-tight">Authorization</h3>
                                    <p class="text-xs text-slate-400 mb-8 leading-relaxed font-medium">Verify all technical manifests before sign-off.</p>
                                    <div class="space-y-4">
                                        <button @click="openApproveModal" 
                                                class="w-full py-4 bg-secondary text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] hover:bg-orange-600 transition-all shadow-2xl shadow-orange-500/20 flex items-center justify-center gap-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            Authorize & Advance
                                        </button>
                                        <button @click="openRejectModal" 
                                                class="w-full py-4 bg-white/10 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] hover:bg-white/20 transition-all border border-white/10 flex items-center justify-center gap-3">
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
                            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-6 space-y-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h2 class="text-[11px] font-black text-navy-900 uppercase tracking-[0.2em]">Engineering Manifest</h2>
                                    <button onclick="window.location='{{ route('dcr.show', $dcr->id) }}?download=all'" 
                                            class="text-[9px] font-black text-secondary hover:text-orange-700 bg-orange-50 px-3 py-2 rounded-xl border border-orange-100 flex items-center gap-2 transition-all active:scale-95 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                        </svg>
                                        Download All
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    @forelse($attachments as $file)
                                    <div class="flex items-center justify-between p-4 border border-slate-100 rounded-3xl cursor-pointer hover:border-secondary transition-all group">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <div class="p-3 rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-secondary group-hover:text-white shrink-0">
                                                @if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['dwg', 'dxf', 'step', 'stp']))
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="font-black text-xs truncate text-navy-900">{{ $file }}</span>
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
                            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8 space-y-6">
                                <h2 class="text-[11px] font-black text-navy-900 uppercase tracking-[0.2em]">Impact Profile</h2>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-5 bg-slate-50 rounded-3xl border border-slate-100">
                                        <span class="text-[9px] text-slate-400 uppercase font-bold block mb-1">Unit Delta</span>
                                        <span class="text-lg font-black text-navy-900">R{{ number_format($impactCost, 2) }}</span>
                                    </div>
                                    <div class="p-5 rounded-3xl border {{ $inventoryScrap ? 'bg-red-50 border-red-100 text-red-600' : 'bg-green-50 border-green-100 text-green-600' }}">
                                        <span class="text-[9px] uppercase font-bold block mb-1 opacity-70">Inventory</span>
                                        <span class="text-lg font-black">{{ $inventoryScrap ? 'SCRAP' : 'OK' }}</span>
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

                            <div class="p-10 space-y-8 text-center">
                                <form @submit.prevent="submitApproval" x-show="!signatureSuccess" class="space-y-8">
                                    <div class="space-y-4">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Engineering Security PIN</label>
                                        <input 
                                            x-model="signaturePin"
                                            type="password"
                                            placeholder="••••"
                                            maxlength="4"
                                            :class="{'border-red-500 bg-red-50 animate-shake': pinError, 'border-slate-100 bg-slate-50 focus:border-secondary focus:bg-white': !pinError}"
                                            class="w-full p-8 text-center text-4xl tracking-[1em] border-[3px] rounded-[2.5rem] outline-none transition-all shadow-inner font-mono"
                                        />
                                    </div>
                                    <div class="flex gap-4">
                                        <button type="button" @click="closeApproveModal" class="flex-1 py-5 text-slate-400 font-black uppercase text-[10px] hover:bg-slate-50 rounded-2xl transition-all">
                                            Abort
                                        </button>
                                        <button type="submit" :disabled="isSigning || signaturePin.length < 4" class="flex-2 px-10 py-5 bg-blue-800 text-white rounded-2xl font-black uppercase text-[10px] shadow-2xl transition-all disabled:opacity-30">
                                            <span x-show="!isSigning">Sign Manifest</span>
                                            <span x-show="isSigning">Securing...</span>
                                        </button>
                                    </div>
                                </form>

                                <div x-show="signatureSuccess" class="py-12 animate-fade-in flex flex-col items-center">
                                    <div class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center mb-8 shadow-inner ring-[15px] ring-green-500/5">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-2xl font-black text-navy-900 mb-2 uppercase tracking-tight">Authorization Verified</h4>
                                </div>
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
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block">Rejection Category</label>
                                    <select x-model="rejectionCategory" required class="w-full p-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-secondary transition-all">
                                        <option value="">Select Category</option>
                                        <option value="compliance">Compliance Issue</option>
                                        <option value="technical">Technical Flaw</option>
                                        <option value="cost">Cost Overrun</option>
                                        <option value="scope">Scope Change</option>
                                    </select>
                                </div>
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block">Detailed Reason</label>
                                    <textarea x-model="rejectionReason" required minlength="10" rows="4" 
                                              class="w-full p-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-secondary transition-all resize-none"
                                              placeholder="Provide detailed technical justification..."></textarea>
                                </div>
                                <div class="flex gap-4">
                                    <button type="button" @click="closeRejectModal" class="flex-1 py-4 text-slate-400 font-black uppercase text-[10px] hover:bg-slate-50 rounded-2xl transition-all">
                                        Cancel
                                    </button>
                                    <button type="submit" :disabled="isRejecting" class="flex-1 py-4 bg-red-600 text-white rounded-2xl font-black uppercase text-[10px] shadow-2xl transition-all disabled:opacity-50">
                                        <span x-show="!isRejecting">Submit Rejection</span>
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
                signaturePin: '',
                isSigning: false,
                pinError: false,
                signatureSuccess: false,
                rejectionCategory: '',
                rejectionReason: '',
                isRejecting: false,
                showToast: false,
                toastMessage: '',

                openApproveModal() {
                    this.showApproveModal = true;
                    this.signaturePin = '';
                    this.pinError = false;
                    this.signatureSuccess = false;
                },

                closeApproveModal() {
                    this.showApproveModal = false;
                    this.signaturePin = '';
                    this.pinError = false;
                    this.signatureSuccess = false;
                },

                openRejectModal() {
                    this.showRejectModal = true;
                    this.rejectionCategory = '';
                    this.rejectionReason = '';
                },

                closeRejectModal() {
                    this.showRejectModal = false;
                },

                async submitApproval() {
                    if (this.signaturePin !== '1234') {
                        this.pinError = true;
                        this.signaturePin = '';
                        return;
                    }

                    this.isSigning = true;

                    try {
                        const response = await fetch('{{ route("dcr.approve", $dcr->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ pin: this.signaturePin })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.signatureSuccess = true;
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            this.pinError = true;
                            this.signaturePin = '';
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.pinError = true;
                    } finally {
                        this.isSigning = false;
                    }
                },

                async submitRejection() {
                    this.isRejecting = true;

                    try {
                        const response = await fetch('{{ route("dcr.reject", $dcr->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                category: this.rejectionCategory,
                                reason: this.rejectionReason
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.toastMessage = 'DCR rejected successfully';
                            this.showToast = true;
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    } catch (error) {
                        console.error('Error:', error);
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
    </style>
</body>
</html>
