<header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 sticky top-0 z-[100] backdrop-blur-md bg-white/80">
    <!-- Search Terminal -->
    <div class="flex-1 max-w-2xl relative" x-data="{ 
        query: '', 
        results: [], 
        isOpen: false,
        async search() {
            if (this.query.trim().length < 2) {
                this.results = [];
                this.isOpen = false;
                return;
            }
            
            try {
                const response = await fetch(`{{ route('dcr.search') }}?q=${encodeURIComponent(this.query)}`);
                const data = await response.json();
                this.results = data;
                this.isOpen = true;
            } catch (error) {
                console.error('Search error:', error);
            }
        },
        handleSelect(id) {
            window.location.href = `/dcr/${id}`;
        },
        closeSearch() {
            this.isOpen = false;
        }
    }" @click.away="closeSearch()">
        <div :class="{ 'scale-[1.01]': isOpen }" class="relative group transition-all duration-300">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary transition-colors">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                type="text"
                x-model="query"
                @input.debounce.300ms="search()"
                @focus="query.length >= 2 && (isOpen = true)"
                placeholder="Global Search (DCR ID, Title, or Description...)"
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3 pl-12 pr-12 text-sm font-medium outline-none focus:ring-4 focus:ring-secondary/10 focus:border-secondary focus:bg-white transition-all placeholder:text-slate-400"
            />
            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1.5 px-1.5 py-1 bg-white border border-slate-200 rounded-md shadow-sm">
                <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-[10px] font-black text-slate-400">K</span>
            </div>
        </div>

        <!-- Search Results Portal -->
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute top-full left-0 w-full mt-3 bg-white rounded-3xl border border-slate-200 shadow-[0_30px_60px_-15px_rgba(15,23,42,0.25)] overflow-hidden z-[110]"
             style="display: none;">
            <div class="p-3 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-3">
                    Indexed Results (<span x-text="results.length"></span>)
                </span>
                <button @click="closeSearch()" class="p-1 hover:bg-slate-100 rounded-lg text-slate-400 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="max-h-[400px] overflow-y-auto p-2 custom-scrollbar">
                <template x-if="results.length > 0">
                    <div>
                        <template x-for="dcr in results" :key="dcr.id">
                            <button 
                                @click="handleSelect(dcr.id)"
                                class="w-full p-4 rounded-2xl hover:bg-slate-50 flex items-start gap-4 transition-all text-left group"
                            >
                                <div class="w-10 h-10 rounded-xl bg-blue-800 flex items-center justify-center text-secondary shadow-sm shrink-0 group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <span class="text-xs font-black text-secondary tracking-widest uppercase" x-text="dcr.dcr_number"></span>
                                        <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-full uppercase tracking-widest" x-text="dcr.status"></span>
                                    </div>
                                    <h4 class="font-extrabold text-navy-900 text-sm truncate mb-1" x-text="dcr.title"></h4>
                                    <p class="text-[11px] text-slate-500 line-clamp-1" x-text="dcr.description"></p>
                                </div>
                                <div class="self-center opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all text-secondary">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="results.length === 0 && query.length >= 2">
                    <div class="p-10 text-center space-y-3">
                        <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No matching manifests found</p>
                    </div>
                </template>
            </div>
            
            <div class="p-4 bg-blue-800 text-white flex justify-between items-center">
                <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest opacity-60">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3 h-3 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        Navigate
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Enter Open
                    </span>
                </div>
                <span class="text-[9px] font-black text-secondary uppercase tracking-[0.2em]">Engineering Core v2.5</span>
            </div>
        </div>
    </div>

    <!-- Profile Actions -->
    <div class="flex items-center gap-6 ml-12">
        <button class="relative p-2.5 text-slate-400 hover:text-navy-900 bg-slate-50 hover:bg-white hover:shadow-sm border border-slate-100 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <div class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white shadow-sm"></div>
        </button>
        
        <div class="h-10 w-px bg-slate-100"></div>
        
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 group cursor-pointer">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-black text-navy-900 tracking-tight group-hover:text-secondary transition-colors">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    {{ auth()->user()->role ?? 'User' }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl border-2 border-white shadow-md group-hover:scale-105 transition-transform bg-blue-600 flex items-center justify-center">
                <span class="text-white font-bold text-lg">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
        </a>
    </div>
</header>

<style>
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
