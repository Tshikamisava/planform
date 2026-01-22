@extends('layouts.app')

@section('title', 'Create New Design Change Request')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 relative pb-12">
    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed top-24 right-8 z-50 bg-navy-900 text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 animate-fade-in border border-slate-700">
        <svg class="w-[18px] h-[18px] text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span class="text-sm font-medium" id="toast-message"></span>
    </div>

    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('dcr.dashboard') }}" class="p-2.5 text-slate-400 hover:text-navy-900 bg-white border border-slate-200 rounded-xl transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-navy-900 tracking-tight">New Design Change Request</h1>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest opacity-60">Manifest ID: <span class="font-mono text-slate-700">DCR-{{ str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT) }}</span></p>
        </div>
    </div>

    <!-- Stepper -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <!-- Progress Bar Background -->
        <div class="relative mb-12">
            <div class="absolute top-5 left-0 right-0 h-1 bg-slate-200 rounded-full"></div>
            <!-- Animated Progress Fill -->
            <div id="stepper-progress" class="absolute top-5 left-0 h-1 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-full shadow-lg shadow-blue-500/50 transition-all duration-500 ease-out" style="width: 0%">
                <!-- Glowing effect at the end -->
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-600 rounded-full animate-pulse shadow-lg shadow-blue-500/50"></div>
            </div>
        </div>

        <div class="flex items-center justify-between relative">
            <div class="flex flex-col items-center flex-1 relative z-10">
                <div class="relative">
                    <div id="pulse-ring-0" class="absolute inset-0 rounded-full bg-blue-600 animate-ping opacity-20"></div>
                    <div class="relative w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500 transform hover:scale-110 bg-gradient-to-br from-blue-600 to-blue-800 text-white shadow-xl shadow-blue-600/60 ring-4 ring-blue-200" id="step-icon-0">
                        <div class="relative">
                            <span class="relative z-10">1</span>
                            <div class="absolute inset-0 bg-white/20 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
                <span class="mt-4 text-[10px] font-bold uppercase tracking-wider transition-all duration-500 text-blue-600 scale-105" id="step-label-0">Details</span>
                <div class="mt-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[8px] font-bold uppercase tracking-wide animate-pulse" id="step-status-0">
                    In Progress
                </div>
            </div>
            
            <div class="flex flex-col items-center flex-1 relative z-10">
                <div class="relative">
                    <div id="pulse-ring-1" class="absolute inset-0 rounded-full bg-blue-600 animate-ping opacity-20 hidden"></div>
                    <div class="relative w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500 transform hover:scale-110 bg-slate-100 text-slate-400" id="step-icon-1">
                        2
                    </div>
                </div>
                <span class="mt-4 text-[10px] font-bold text-center w-28 uppercase tracking-wider transition-all duration-500 text-slate-400" id="step-label-1">Impact Analysis</span>
                <div class="mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-[8px] font-bold uppercase tracking-wide hidden" id="step-status-1">
                    Complete
                </div>
            </div>
            
            <div class="flex flex-col items-center flex-1 relative z-10">
                <div class="relative">
                    <div id="pulse-ring-2" class="absolute inset-0 rounded-full bg-blue-600 animate-ping opacity-20 hidden"></div>
                    <div class="relative w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500 transform hover:scale-110 bg-slate-100 text-slate-400" id="step-icon-2">
                        3
                    </div>
                </div>
                <span class="mt-4 text-[10px] font-bold text-center w-28 uppercase tracking-wider transition-all duration-500 text-slate-400" id="step-label-2">Attachments</span>
                <div class="mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-[8px] font-bold uppercase tracking-wide hidden" id="step-status-2">
                    Complete
                </div>
            </div>
        </div>

        <!-- Progress Percentage -->
        <div class="mt-8 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-full border border-blue-200">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                <span class="text-sm font-bold text-blue-900" id="progress-percentage">
                    0% Complete
                </span>
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form id="dcr-form" action="{{ route('dcr.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Content Card -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8 min-h-[400px]">
            <!-- Step 0: Details -->
            <div id="step-0" class="space-y-6 animate-fade-in">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Title of Change</label>
                    <input 
                        type="text" 
                        name="title"
                        value="{{ old('title') }}"
                        class="w-full p-4 rounded-xl border border-slate-200 focus:border-secondary focus:ring-4 focus:ring-secondary/10 outline-none transition-all"
                        placeholder="e.g. Bracing Update for XJ-200"
                        data-required="true"
                    />
                    <p class="text-red-500 text-[10px] font-bold uppercase mt-1 hidden" data-error="title">Title is required for compliance</p>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Reason for Change</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach(['Cost Reduction', 'Quality', 'Performance', 'Compliance', 'Safety', 'Customer Request', 'Obsolescence', 'Other'] as $reason)
                            <button
                                type="button"
                                data-reason="{{ $reason }}"
                                onclick="selectReason(this, '{{ $reason }}')"
                                class="reason-btn p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-500 bg-white text-xs font-bold transition-all"
                            >
                                {{ $reason }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="reason" id="reason-input" value="{{ old('reason', 'Cost Reduction') }}">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Description</label>
                    <textarea 
                        name="description"
                        class="w-full p-4 rounded-xl border border-slate-200 focus:border-secondary focus:ring-4 focus:ring-secondary/10 outline-none transition-all h-32 resize-none"
                        placeholder="Describe technical details of the change..."
                        data-required="true"
                    >{{ old('description') }}</textarea>
                    <p class="text-red-500 text-[10px] font-bold uppercase mt-1 hidden" data-error="description">Description is required</p>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Affected Parts</label>
                        <button type="button" onclick="addPartRow()" class="text-[10px] text-secondary font-extrabold uppercase hover:text-orange-700 flex items-center gap-1 bg-orange-50 px-2 py-1 rounded">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Row
                        </button>
                    </div>
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-inner bg-slate-50/30">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <tr>
                                    <th class="px-4 py-3">Part Number</th>
                                    <th class="px-4 py-3 w-32">Current Rev</th>
                                    <th class="px-4 py-3 w-32">New Rev</th>
                                    <th class="px-4 py-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white" id="parts-tbody">
                                <tr class="part-row">
                                    <td class="p-2">
                                        <input 
                                            name="parts[0][number]"
                                            class="part-number w-full p-2.5 rounded-lg border border-slate-100 focus:border-secondary outline-none text-xs font-medium transition-all"
                                            placeholder="PN-000"
                                            data-required="true"
                                        />
                                    </td>
                                    <td class="p-2">
                                        <input name="parts[0][current_rev]" value="A" class="w-full p-2.5 rounded-lg border border-slate-100 outline-none text-xs text-slate-500" />
                                    </td>
                                    <td class="p-2">
                                        <input name="parts[0][new_rev]" value="B" class="w-full p-2.5 rounded-lg border border-slate-100 outline-none bg-blue-50 text-blue-700 font-bold text-xs" />
                                    </td>
                                    <td class="p-2 text-center">
                                        <button type="button" onclick="removePartRow(this)" class="text-slate-300 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Step 1: Impact Analysis -->
            <div id="step-1" class="space-y-8 animate-fade-in hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Est. Cost Impact (Unit)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">R</span>
                            <input 
                                type="number"
                                name="cost"
                                value="{{ old('cost') }}"
                                step="0.01"
                                class="w-full pl-10 p-4 rounded-xl border border-slate-200 outline-none transition-all focus:border-secondary focus:ring-4 focus:ring-secondary/10"
                                placeholder="0.00"
                                data-required="true"
                            />
                        </div>
                        <p class="text-red-500 text-[10px] font-bold uppercase mt-1 hidden" data-error="cost">Cost estimate is required</p>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Weight Change (kg)</label>
                        <input 
                            type="number"
                            name="weight"
                            value="{{ old('weight') }}"
                            step="0.01"
                            class="w-full p-4 rounded-xl border border-slate-200 outline-none transition-all focus:border-secondary focus:ring-4 focus:ring-secondary/10"
                            placeholder="+/- 0.00"
                        />
                    </div>
                </div>

                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-5">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold text-slate-700">Tooling Impact?</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input 
                                    type="radio" 
                                    name="tooling"
                                    value="1"
                                    onclick="toggleToolingDesc(true)"
                                    class="accent-secondary w-4 h-4"
                                /> 
                                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input 
                                    type="radio" 
                                    name="tooling"
                                    value="0"
                                    checked
                                    onclick="toggleToolingDesc(false)"
                                    class="accent-secondary w-4 h-4"
                                /> 
                                <span class="text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">No</span>
                            </label>
                        </div>
                    </div>

                    <div id="tooling-desc-container" class="hidden animate-fade-in pl-1 space-y-2">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">
                            Describe Tooling Changes <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="tooling_desc"
                            id="tooling-desc"
                            class="w-full p-4 rounded-xl bg-white border border-slate-200 outline-none transition-all shadow-sm focus:border-secondary"
                            placeholder="Describe modifications to molds, jigs, or automation..."
                            rows="3"
                        >{{ old('tooling_desc') }}</textarea>
                        <p class="text-red-500 text-[10px] font-bold uppercase tracking-tight flex items-center gap-1 hidden" data-error="tooling_desc">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tooling description is mandatory for impact assessment
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 p-6 border border-slate-200 rounded-2xl bg-white group cursor-pointer hover:border-secondary/30 transition-all">
                    <input 
                        type="checkbox" 
                        name="inventory_scrap"
                        value="1"
                        {{ old('inventory_scrap') ? 'checked' : '' }}
                        class="w-6 h-6 accent-secondary rounded-lg"
                    />
                    <div>
                        <p class="text-sm font-bold text-slate-800">Scrap Existing Inventory?</p>
                        <p class="text-xs text-slate-400 mt-1">Check if current stock must be quarantined or scrapped per quality control standards.</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Attachments -->
            <div id="step-2" class="space-y-8 animate-fade-in hidden">
                <input type="file" multiple class="hidden" id="file-input" name="attachments[]" accept=".pdf,.png,.dxf,.step,.stp">
                <div 
                    onclick="document.getElementById('file-input').click()"
                    class="border-3 border-dashed border-slate-200 rounded-[2.5rem] p-12 flex flex-col items-center justify-center text-center hover:bg-slate-50 hover:border-secondary/50 transition-all cursor-pointer group relative overflow-hidden"
                >
                    <div class="absolute inset-0 bg-secondary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-20 h-20 bg-orange-50 text-secondary rounded-[1.5rem] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-sm relative z-10">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 relative z-10">Transmit Technical Documentation</h3>
                    <p class="text-sm text-slate-500 mt-2 mb-6 relative z-10">Drag & drop files or click to browse CAD/PDF data</p>
                    <div class="flex gap-2 relative z-10">
                        @foreach(['.pdf', '.png', '.dxf', '.step', '.stp'] as $ext)
                            <span class="text-[9px] font-black text-slate-400 bg-white border border-slate-100 px-2.5 py-1.5 rounded-lg uppercase tracking-widest shadow-sm">{{ str_replace('.', '', $ext) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Asset Pipeline (<span id="file-count">0</span>)</h4>
                        <button type="button" onclick="clearAllFiles()" class="text-[10px] font-bold text-red-500 uppercase hover:underline hidden" id="clear-all-btn">Clear All</button>
                    </div>

                    <div id="files-container" class="text-center text-sm text-slate-400 py-16 italic border border-dashed border-slate-100 rounded-3xl bg-slate-50/50">
                        No technical assets attached to this manifest.
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-6 px-4">
            <button 
                type="button"
                onclick="handleBack()"
                id="back-btn"
                class="px-8 py-3 rounded-xl text-slate-500 font-bold uppercase tracking-widest text-[11px] hover:bg-slate-100 transition-colors"
            >
                Abort
            </button>

            <div class="flex gap-4">
                <button 
                    type="button"
                    onclick="saveDraft()"
                    class="px-6 py-3 rounded-xl text-slate-600 font-bold uppercase tracking-widest text-[11px] hover:bg-slate-100 transition-all flex items-center gap-2 border border-slate-200 shadow-sm bg-white"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Save Draft
                </button>
                <button 
                    type="button"
                    onclick="handleNext()"
                    id="next-btn"
                    class="px-10 py-3.5 bg-secondary hover:bg-orange-600 text-white rounded-xl font-extrabold uppercase tracking-widest text-[11px] shadow-xl shadow-orange-500/20 transition-all flex items-center gap-2 active:scale-95"
                >
                    Continue
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </form>

    <!-- Success Modal -->
    <div id="success-modal" class="hidden fixed inset-0 bg-navy-900/90 backdrop-blur-xl z-[100] flex items-center justify-center p-4 animate-fade-in">
        <div class="bg-white rounded-[3rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] p-12 max-w-md w-full text-center border border-slate-100">
            <div class="w-24 h-24 bg-green-50 text-green-500 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner ring-12 ring-green-500/5">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-navy-900 mb-3 tracking-tight">Transmission Successful</h2>
            <p class="text-slate-500 text-sm leading-relaxed mb-10">
                Your manifest has been indexed and routed for Engineering Review.
            </p>
            <a href="{{ route('dcr.dashboard') }}" class="inline-block w-full py-4 bg-navy-900 text-white rounded-2xl font-bold hover:bg-navy-800 transition-all shadow-xl shadow-navy-900/20 uppercase tracking-widest text-xs">
                View Request Terminal
            </a>
        </div>
    </div>
</div>

<script>
let currentStep = 0;
let partCounter = 0;
let uploadedFiles = [];

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Load draft if exists
    const draft = localStorage.getItem('PLANFORM_DCR_DRAFT');
    if (draft) {
        try {
            const data = JSON.parse(draft);
            loadDraft(data);
            showToast('Draft loaded from local storage');
        } catch (e) {
            console.error('Failed to load draft', e);
        }
    }

    // Set initial reason
    const firstReasonBtn = document.querySelector('[data-reason]');
    if (firstReasonBtn) {
        selectReason(firstReasonBtn, firstReasonBtn.dataset.reason);
    }

    // File input handler
    document.getElementById('file-input').addEventListener('change', handleFileSelect);
});

function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    toastMessage.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

function selectReason(btn, reason) {
    document.querySelectorAll('.reason-btn').forEach(b => {
        b.classList.remove('border-secondary', 'bg-orange-50', 'text-secondary', 'shadow-sm', 'shadow-orange-500/10');
        b.classList.add('border-slate-200', 'text-slate-500', 'bg-white');
    });
    btn.classList.remove('border-slate-200', 'text-slate-500', 'bg-white');
    btn.classList.add('border-secondary', 'bg-orange-50', 'text-secondary', 'shadow-sm', 'shadow-orange-500/10');
    document.getElementById('reason-input').value = reason;
}

function addPartRow() {
    partCounter++;
    const tbody = document.getElementById('parts-tbody');
    const row = document.createElement('tr');
    row.className = 'part-row';
    row.innerHTML = `
        <td class="p-2">
            <input 
                name="parts[${partCounter}][number]"
                class="part-number w-full p-2.5 rounded-lg border border-slate-100 focus:border-secondary outline-none text-xs font-medium transition-all"
                placeholder="PN-000"
                data-required="true"
            />
        </td>
        <td class="p-2">
            <input name="parts[${partCounter}][current_rev]" value="A" class="w-full p-2.5 rounded-lg border border-slate-100 outline-none text-xs text-slate-500" />
        </td>
        <td class="p-2">
            <input name="parts[${partCounter}][new_rev]" value="B" class="w-full p-2.5 rounded-lg border border-slate-100 outline-none bg-blue-50 text-blue-700 font-bold text-xs" />
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removePartRow(this)" class="text-slate-300 hover:text-red-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

function removePartRow(btn) {
    const rows = document.querySelectorAll('.part-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
    }
}

function toggleToolingDesc(show) {
    const container = document.getElementById('tooling-desc-container');
    if (show) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
        document.getElementById('tooling-desc').value = '';
    }
}

function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    const allowedExts = ['.pdf', '.png', '.dxf', '.step', '.stp'];
    const maxSize = 50 * 1024 * 1024; // 50MB

    files.forEach(file => {
        const ext = '.' + file.name.split('.').pop().toLowerCase();
        const isValid = allowedExts.includes(ext);
        const isTooLarge = file.size > maxSize;

        const fileObj = {
            id: Math.random().toString(36).substring(7),
            file: file,
            status: (isValid && !isTooLarge) ? 'success' : 'error',
            error: !isValid ? `Invalid format. Only ${allowedExts.join(', ')} are permitted.` : 
                   isTooLarge ? `File size exceeds ${maxSize / 1024 / 1024}MB.` : null
        };

        uploadedFiles.push(fileObj);
    });

    renderFiles();
}

function renderFiles() {
    const container = document.getElementById('files-container');
    const countSpan = document.getElementById('file-count');
    const clearBtn = document.getElementById('clear-all-btn');

    countSpan.textContent = uploadedFiles.length;

    if (uploadedFiles.length === 0) {
        container.innerHTML = 'No technical assets attached to this manifest.';
        container.className = 'text-center text-sm text-slate-400 py-16 italic border border-dashed border-slate-100 rounded-3xl bg-slate-50/50';
        clearBtn.classList.add('hidden');
        return;
    }

    clearBtn.classList.remove('hidden');
    container.className = 'space-y-4';
    container.innerHTML = uploadedFiles.map((item, index) => `
        <div class="p-6 bg-white rounded-3xl border ${item.status === 'error' ? 'border-red-200 bg-red-50/10' : 'border-green-100'} transition-all duration-300 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 ${item.status === 'error' ? 'bg-red-500' : 'bg-green-500'}"></div>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-5 flex-1 min-w-0">
                    <div class="p-3.5 rounded-2xl shadow-sm ${item.status === 'error' ? 'bg-red-100 text-red-600' : 'bg-green-50 text-green-600'} shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${item.status === 'error' ? 
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                            }
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h5 class="text-sm font-black text-navy-900 truncate leading-tight">${item.file.name}</h5>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">${(item.file.size / 1024 / 1024).toFixed(2)} MB • Engineering Asset</p>
                        ${item.error ? `
                            <div class="flex items-start gap-2 text-red-600 mt-2">
                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-[10px] font-bold leading-tight uppercase">${item.error}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
                <button type="button" onclick="removeFile(${index})" class="p-2.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all ml-6 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    `).join('');
}

function removeFile(index) {
    uploadedFiles.splice(index, 1);
    renderFiles();
}

function clearAllFiles() {
    uploadedFiles = [];
    document.getElementById('file-input').value = '';
    renderFiles();
}

function validateStep(step) {
    let isValid = true;
    const errors = document.querySelectorAll('[data-error]');
    errors.forEach(el => el.classList.add('hidden'));

    if (step === 0) {
        const title = document.querySelector('[name="title"]');
        const description = document.querySelector('[name="description"]');
        const partNumbers = document.querySelectorAll('.part-number');

        if (!title.value.trim()) {
            document.querySelector('[data-error="title"]').classList.remove('hidden');
            title.classList.add('border-red-500', 'bg-red-50');
            isValid = false;
        } else {
            title.classList.remove('border-red-500', 'bg-red-50');
        }

        if (!description.value.trim()) {
            document.querySelector('[data-error="description"]').classList.remove('hidden');
            description.classList.add('border-red-500', 'bg-red-50');
            isValid = false;
        } else {
            description.classList.remove('border-red-500', 'bg-red-50');
        }

        partNumbers.forEach((input, idx) => {
            if (!input.value.trim()) {
                input.classList.add('border-red-300', 'bg-red-50');
                isValid = false;
            } else {
                input.classList.remove('border-red-300', 'bg-red-50');
            }
        });
    }

    if (step === 1) {
        const cost = document.querySelector('[name="cost"]');
        if (!cost.value) {
            document.querySelector('[data-error="cost"]').classList.remove('hidden');
            cost.classList.add('border-red-500');
            isValid = false;
        } else {
            cost.classList.remove('border-red-500');
        }

        const toolingYes = document.querySelector('[name="tooling"][value="1"]');
        const toolingDesc = document.querySelector('[name="tooling_desc"]');
        if (toolingYes && toolingYes.checked && !toolingDesc.value.trim()) {
            document.querySelector('[data-error="tooling_desc"]').classList.remove('hidden');
            toolingDesc.classList.add('border-red-500', 'bg-red-50');
            isValid = false;
        } else {
            toolingDesc.classList.remove('border-red-500', 'bg-red-50');
        }
    }

    if (step === 2) {
        const hasErrors = uploadedFiles.some(f => f.status === 'error');
        if (hasErrors) {
            showToast('Please resolve file upload errors before submitting.');
            isValid = false;
        }
    }

    return isValid;
}

function handleNext() {
    if (!validateStep(currentStep)) {
        return;
    }

    if (currentStep < 2) {
        updateStep(currentStep + 1);
    } else {
        submitForm();
    }
}

function handleBack() {
    if (currentStep === 0) {
        window.location.href = '{{ route("dcr.dashboard") }}';
    } else {
        updateStep(currentStep - 1);
    }
}

function updateStep(newStep) {
    // Hide current step
    document.getElementById(`step-${currentStep}`).classList.add('hidden');
    
    // Calculate progress percentage
    const progressPercent = (newStep / 2) * 100;
    document.getElementById('stepper-progress').style.width = progressPercent + '%';
    document.getElementById('progress-percentage').textContent = Math.round(progressPercent) + '% Complete';
    
    // Update step icons and labels
    for (let i = 0; i <= 2; i++) {
        const icon = document.getElementById(`step-icon-${i}`);
        const label = document.getElementById(`step-label-${i}`);
        const status = document.getElementById(`step-status-${i}`);
        const pulseRing = document.getElementById(`pulse-ring-${i}`);

        if (i < newStep) {
            // Completed step
            icon.className = 'relative w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500 transform hover:scale-110 bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-500/50';
            icon.innerHTML = '<svg class="w-6 h-6 animate-[bounce_1s_ease-in-out]" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
            label.className = 'mt-4 text-[10px] font-bold text-center w-28 uppercase tracking-wider transition-all duration-500 text-slate-600';
            status.className = 'mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-[8px] font-bold uppercase tracking-wide';
            status.classList.remove('hidden');
            pulseRing.classList.add('hidden');
        } else if (i === newStep) {
            // Current step
            icon.className = 'relative w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500 transform hover:scale-110 bg-gradient-to-br from-blue-600 to-blue-800 text-white shadow-xl shadow-blue-600/60 ring-4 ring-blue-200';
            icon.innerHTML = `<div class="relative"><span class="relative z-10">${i + 1}</span><div class="absolute inset-0 bg-white/20 rounded-full animate-pulse"></div></div>`;
            label.className = 'mt-4 text-[10px] font-bold text-center w-28 uppercase tracking-wider transition-all duration-500 text-blue-600 scale-105';
            status.textContent = 'In Progress';
            status.className = 'mt-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[8px] font-bold uppercase tracking-wide animate-pulse';
            status.classList.remove('hidden');
            pulseRing.classList.remove('hidden');
        } else {
            // Future step
            icon.className = 'relative w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-500 transform hover:scale-110 bg-slate-100 text-slate-400';
            icon.textContent = i + 1;
            label.className = 'mt-4 text-[10px] font-bold text-center w-28 uppercase tracking-wider transition-all duration-500 text-slate-400';
            status.classList.add('hidden');
            pulseRing.classList.add('hidden');
        }
    }

    // Show new step
    document.getElementById(`step-${newStep}`).classList.remove('hidden');
    
    // Update buttons
    const backBtn = document.getElementById('back-btn');
    const nextBtn = document.getElementById('next-btn');
    
    backBtn.textContent = newStep === 0 ? 'Abort' : 'Back';
    
    if (newStep === 2) {
        nextBtn.innerHTML = 'Publish Manifest';
    } else {
        nextBtn.innerHTML = 'Continue <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
    }

    currentStep = newStep;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function saveDraft() {
    const formData = new FormData(document.getElementById('dcr-form'));
    const data = Object.fromEntries(formData.entries());
    localStorage.setItem('PLANFORM_DCR_DRAFT', JSON.stringify(data));
    showToast('Draft saved successfully!');
}

function loadDraft(data) {
    // Implement draft loading logic
    if (data.title) document.querySelector('[name="title"]').value = data.title;
    if (data.description) document.querySelector('[name="description"]').value = data.description;
    // Add more fields as needed
}

function submitForm() {
    const nextBtn = document.getElementById('next-btn');
    nextBtn.disabled = true;
    nextBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    setTimeout(() => {
        document.getElementById('dcr-form').submit();
        document.getElementById('success-modal').classList.remove('hidden');
        localStorage.removeItem('PLANFORM_DCR_DRAFT');
    }, 1500);
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
