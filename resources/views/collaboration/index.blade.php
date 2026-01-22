<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Collaboration - Planform DCR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        @keyframes message-slide-in {
            from { 
                opacity: 0; 
                transform: translateY(20px) scale(0.95);
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1);
            }
        }
        .message-enter {
            animation: message-slide-in 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-slate-50">
    <div class="flex h-screen overflow-hidden" x-data="collaborationManager()">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('layouts.header')

            <!-- Collaboration Content -->
            <main class="flex-1 overflow-hidden bg-slate-50 p-8">
                <div class="h-full flex bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xl animate-fade-in">
                    
                    <!-- Left Sidebar -->
                    <div class="w-96 border-r border-slate-100 flex flex-col bg-white">
                        <div class="p-6 border-b border-slate-100">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-black text-slate-900">COMMS</h2>
                                <button class="p-2 bg-secondary text-white rounded-xl hover:bg-orange-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative">
                                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input 
                                    type="text" 
                                    x-model="searchQuery"
                                    placeholder="Filter streams..." 
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all placeholder:text-slate-400"
                                />
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-6">
                            <!-- Project Streams -->
                            <div>
                                <div class="flex items-center justify-between px-3 mb-3">
                                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Project Streams</h3>
                                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    @foreach($groups as $index => $contact)
                                    <div 
                                        @click="selectContact({{ json_encode($contact) }})"
                                        :class="selectedContact?.id === '{{ $contact['id'] }}' ? 'bg-blue-800 text-white shadow-lg' : 'hover:bg-slate-50'"
                                        class="group p-3.5 rounded-2xl flex gap-3 cursor-pointer transition-all"
                                    >
                                        <div class="relative shrink-0">
                                            @if($index == 0)
                                            <div class="w-12 h-12 rounded-xl bg-blue-800 flex items-center justify-center text-white font-bold text-base">
                                                XB
                                            </div>
                                            @else
                                            <div class="w-12 h-12 rounded-xl bg-secondary flex items-center justify-center text-white font-bold text-base">
                                                QC
                                            </div>
                                            @endif
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white rounded-full flex items-center justify-center">
                                                <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4 :class="selectedContact?.id === '{{ $contact['id'] }}' ? 'text-white' : 'text-slate-900'" 
                                                    class="font-bold text-sm">{{ $contact['name'] }}</h4>
                                                <span :class="selectedContact?.id === '{{ $contact['id'] }}' ? 'text-white/60' : 'text-slate-400'" 
                                                      class="text-[11px] font-medium">9:05 AM</span>
                                            </div>
                                            <p :class="selectedContact?.id === '{{ $contact['id'] }}' ? 'text-white/70' : 'text-slate-500'" 
                                               class="text-[13px] truncate">{{ $contact['lastMessage'] }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Stakeholders -->
                            <div>
                                <h3 class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Stakeholders</h3>
                                <div class="space-y-2">
                                    @foreach($users as $contact)
                                    <div 
                                        @click="selectContact({{ json_encode($contact) }})"
                                        class="group p-3 rounded-2xl hover:bg-slate-50 flex gap-3 cursor-pointer transition-all"
                                    >
                                        <div class="relative shrink-0">
                                            <img src="{{ $contact['avatar'] }}" 
                                                 class="w-10 h-10 rounded-xl" alt="">
                                            <div x-show="{{ $contact['online'] ? 'true' : 'false' }}" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-0.5">
                                                <h4 class="font-semibold text-sm text-slate-900">{{ $contact['name'] }}</h4>
                                                <span class="text-[11px] text-slate-500 font-medium">10:20 AM</span>
                                            </div>
                                            <p class="text-[13px] text-slate-600 truncate">{{ $contact['lastMessage'] }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Chat Interface -->
                    <div class="flex-1 flex flex-col bg-white relative z-10">
                        
                        <!-- Thread Header -->
                        <div class="px-6 h-18 border-b border-slate-200 flex items-center justify-between bg-white">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-800 flex items-center justify-center text-white font-bold text-lg">
                                    XB
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base" x-text="selectedContact?.name || '{{ $contacts->first()['name'] }}'"></h3>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span class="text-xs font-semibold text-green-600 uppercase tracking-wide">Active Connection</span>
                                        </div>
                                        <span class="text-xs text-slate-500">12 Members</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </button>
                                <button class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <button class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Message Viewport -->
                        <div x-ref="messageContainer" class="flex-1 overflow-y-auto p-8 space-y-6 bg-slate-50 custom-scrollbar">
                            <!-- System Status Divider -->
                            <div class="flex items-center justify-center py-6">
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.25em]">System Link Established</span>
                            </div>

                            <template x-for="msg in messages" :key="msg.id">
                                <div class="flex justify-start">
                                    <div class="flex flex-col items-start max-w-[65%] space-y-2">
                                        <!-- Message Content -->
                                        <div class="bg-white text-slate-800 px-5 py-3.5 rounded-2xl shadow-sm message-enter">
                                            
                                            <!-- Audio Message -->
                                            <template x-if="msg.type === 'audio'">
                                                <div class="flex items-center gap-4 min-w-[240px]" x-data="{ playing: false, progress: 0 }">
                                                    <button 
                                                        @click="playing = !playing; if(playing) { let interval = setInterval(() => { progress += 2; if(progress >= 100) { playing = false; clearInterval(interval); progress = 0; } }, 100) }"
                                                        class="p-2.5 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all shadow-sm"
                                                    >
                                                        <svg x-show="!playing" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                            </svg>
                                                        <svg x-show="playing" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                                                        </svg>
                                                    </button>
                                                    <div class="flex-1">
                                                        <div class="h-1.5 bg-slate-200 rounded-full w-full relative overflow-hidden">
                                                            <div 
                                                                class="h-full bg-slate-400 transition-all duration-100"
                                                                :style="'width: ' + progress + '%'"
                                                            ></div>
                                                        </div>
                                                        <div class="flex justify-between items-center text-[10px] text-slate-500 mt-1.5 font-medium">
                                                            <span x-text="msg.audioDuration"></span>
                                                            <span class="text-[9px] uppercase tracking-wider">Audio Message</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Text Message -->
                                            <template x-if="msg.type === 'text'">
                                                <p class="leading-relaxed text-[14px]" x-text="msg.text"></p>
                                            </template>
                                        </div>
                                        
                                        <!-- Timestamp -->
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="msg.timestamp"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Interaction Bar -->
                        <div class="p-8 bg-white border-t border-slate-100">
                            <div class="flex items-center gap-4 p-4 rounded-3xl bg-slate-50 border border-slate-200 transition-all focus-within:bg-white focus-within:border-slate-300 focus-within:shadow-lg">
                                <button class="p-2.5 text-slate-400 hover:text-slate-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                </button>
                                
                                <template x-if="isRecording">
                                    <div class="flex-1 flex items-center justify-between px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                <div class="absolute inset-0 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                                            </div>
                                            <span class="text-sm font-semibold text-slate-700">Recording...</span>
                                        </div>
                                        <div class="font-mono font-semibold text-slate-600 text-lg" x-text="'0:' + recordingTime.toString().padStart(2, '0')"></div>
                                    </div>
                                </template>

                                <template x-if="!isRecording">
                                    <input 
                                        type="text" 
                                        x-model="inputText"
                                        @keyup.enter="sendMessage"
                                        placeholder="Stream technical input..." 
                                        class="flex-1 bg-transparent text-[15px] py-2 px-2 outline-none text-slate-700 placeholder:text-slate-400"
                                    />
                                </template>

                                <div class="flex items-center gap-2">
                                    <template x-if="isRecording">
                                        <button 
                                            @click="stopRecording"
                                            class="p-3 bg-slate-700 text-white rounded-full hover:bg-slate-800 transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <rect x="6" y="6" width="12" height="12" rx="2"/>
                                            </svg>
                                        </button>
                                    </template>

                                    <template x-if="!isRecording">
                                        <div class="flex items-center gap-2">
                                            <button 
                                                @click="startRecording"
                                                class="p-2.5 text-slate-400 hover:text-slate-600 transition-all"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                                </svg>
                                            </button>
                                            <button 
                                                @click="sendMessage"
                                                :disabled="!inputText.trim()"
                                                class="p-3 bg-slate-700 text-white rounded-full hover:bg-slate-800 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Board Members Sidebar -->
                    <div class="w-80 border-l border-slate-200 bg-white">
                        <div class="px-6 py-5 border-b border-slate-200">
                            <h4 class="text-[11px] font-semibold text-slate-400 uppercase tracking-[0.15em]">Board Members</h4>
                        </div>
                        <div class="overflow-y-auto px-6 py-4 space-y-4 custom-scrollbar">
                            @foreach($users->take(3) as $person)
                            <div class="flex items-center gap-3">
                                <img src="{{ $person['avatar'] }}" 
                                     class="w-11 h-11 rounded-xl" 
                                     alt="{{ $person['name'] }}">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate mb-0.5">{{ $person['name'] }}</p>
                                    <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide truncate">{{ $person['role'] ?? 'Member' }}</p>
                                </div>
                            </div>
                            @endforeach
                            <button class="w-full py-2.5 mt-2 bg-slate-50 rounded-xl text-[11px] font-semibold text-slate-600 hover:bg-slate-100 transition-all uppercase tracking-wide">
                                View All 12 Members
                            </button>
                        </div>

                        <div class="mx-6 mb-6 p-5 bg-blue-800 rounded-2xl">
                            <h5 class="text-[10px] font-bold text-secondary uppercase tracking-wider mb-2">Comms Tip</h5>
                            <p class="text-[13px] leading-relaxed text-slate-300">Use voice logs for complex structural feedback to reduce documentation cycle time by up to 30%.</p>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        function collaborationManager() {
            return {
                selectedContact: @json($contacts->first()),
                messages: @json($mockMessages),
                inputText: '',
                isRecording: false,
                recordingTime: 0,
                searchQuery: '',
                recordingInterval: null,

                selectContact(contact) {
                    this.selectedContact = contact;
                    // In production, fetch messages for this contact
                    console.log('Selected contact:', contact);
                },

                sendMessage() {
                    if (!this.inputText.trim()) return;

                    const newMessage = {
                        id: Date.now(),
                        sender: '{{ $currentUser->name }}',
                        text: this.inputText,
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        isMe: true,
                        read: false,
                        type: 'text'
                    };

                    this.messages.push(newMessage);
                    this.inputText = '';

                    this.$nextTick(() => {
                        this.$refs.messageContainer.scrollTop = this.$refs.messageContainer.scrollHeight;
                    });
                },

                startRecording() {
                    this.isRecording = true;
                    this.recordingTime = 0;
                    this.recordingInterval = setInterval(() => {
                        this.recordingTime++;
                    }, 1000);
                },

                stopRecording() {
                    this.isRecording = false;
                    clearInterval(this.recordingInterval);

                    const audioMessage = {
                        id: Date.now(),
                        sender: '{{ $currentUser->name }}',
                        audioUrl: '#',
                        audioDuration: '0:' + this.recordingTime.toString().padStart(2, '0'),
                        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                        isMe: true,
                        read: false,
                        type: 'audio'
                    };

                    this.messages.push(audioMessage);
                    this.recordingTime = 0;

                    this.$nextTick(() => {
                        this.$refs.messageContainer.scrollTop = this.$refs.messageContainer.scrollHeight;
                    });
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
