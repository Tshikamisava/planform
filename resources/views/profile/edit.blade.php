<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8 pb-12">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-blue-900">User Settings</h1>
                <p class="text-slate-500">Manage your profile and platform preferences.</p>
            </div>
            <button type="submit" form="settings-form" class="px-6 py-2 bg-blue-800 text-white rounded-lg font-bold text-sm shadow-lg hover:bg-blue-700 transition-all">
                Save Changes
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Navigation Sidebar -->
            <div class="space-y-1">
                <a href="#profile" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium bg-white text-blue-800 transition-all">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                </a>
                <a href="#notifications" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-white hover:text-blue-800 transition-all">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    Notifications
                </a>
                <a href="#security" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-white hover:text-blue-800 transition-all">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Security
                </a>
                <a href="#appearance" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-white hover:text-blue-800 transition-all">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    Appearance
                </a>
                <a href="#integration" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-500 hover:bg-white hover:text-blue-800 transition-all">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Integrations
                </a>
                
                <div class="pt-4 mt-4 border-t border-slate-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 transition-all">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <div class="md:col-span-3 space-y-6">
                <form id="settings-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- Public Profile Section -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h3 class="font-bold text-blue-900 mb-6 pb-2 border-b border-slate-50">Public Profile</h3>
                        
                        <div class="flex items-center gap-6 mb-8">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" class="w-20 h-20 rounded-2xl border-2 border-slate-100 shadow-sm object-cover" alt="Profile">
                            @else
                                <div class="w-20 h-20 rounded-2xl border-2 border-slate-100 shadow-sm bg-blue-100 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-blue-800">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <label for="avatar" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all cursor-pointer inline-block">
                                    Change Avatar
                                </label>
                                <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*">
                                <p class="text-xs text-slate-400 mt-2">Recommended size: 256x256px. JPG or PNG.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Job Title</label>
                                <input type="text" name="role" value="{{ old('role', auth()->user()->role) }}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500/20 transition-all">
                            </div>
                            <div class="space-y-2 col-span-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance Section -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mt-6">
                        <h3 class="font-bold text-blue-900 mb-6 pb-2 border-b border-slate-50">Appearance</h3>
                        <div class="space-y-4">
                            <label class="text-xs font-bold text-slate-500 uppercase">Interface Theme</label>
                            <div class="grid grid-cols-3 gap-4">
                                <button type="button" onclick="setTheme('light')" class="p-4 rounded-xl border-2 border-slate-100 hover:border-slate-200 transition-all flex flex-col items-center gap-2">
                                    <div class="w-full h-12 rounded-lg bg-slate-100"></div>
                                    <span class="text-xs font-bold text-slate-700">Light</span>
                                </button>
                                <button type="button" onclick="setTheme('dark')" class="p-4 rounded-xl border-2 border-slate-100 hover:border-slate-200 transition-all flex flex-col items-center gap-2">
                                    <div class="w-full h-12 rounded-lg bg-blue-900"></div>
                                    <span class="text-xs font-bold text-slate-700">Dark</span>
                                </button>
                                <button type="button" onclick="setTheme('system')" class="p-4 rounded-xl border-2 border-slate-100 hover:border-slate-200 transition-all flex flex-col items-center gap-2">
                                    <div class="w-full h-12 rounded-lg bg-gradient-to-r from-slate-100 to-blue-900"></div>
                                    <span class="text-xs font-bold text-slate-700">System</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Section -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mt-6">
                        <h3 class="font-bold text-blue-900 mb-4 flex items-center justify-between">
                            Notifications
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="notifications_enabled" value="1" class="sr-only peer" checked>
                                <div class="w-12 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </h3>
                        <p class="text-sm text-slate-500">Receive email alerts for DCR status transitions and chat messages.</p>
                    </div>
                </form>

                <!-- Password Change Section -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-blue-900 mb-6 pb-2 border-b border-slate-50">Change Password</h3>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Current Password</label>
                                <input type="password" name="current_password" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">New Password</label>
                                <input type="password" name="password" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500/20 transition-all" required>
                            </div>
                            <button type="submit" class="px-6 py-2 bg-blue-800 text-white rounded-lg font-bold text-sm shadow-lg hover:bg-blue-700 transition-all">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setTheme(theme) {
            // Remove active class from all theme buttons
            document.querySelectorAll('[onclick^="setTheme"]').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50');
                btn.classList.add('border-slate-100');
            });
            
            // Add active class to clicked button
            event.target.closest('button').classList.remove('border-slate-100');
            event.target.closest('button').classList.add('border-blue-500', 'bg-blue-50');
            
            // Store theme preference
            localStorage.setItem('theme', theme);
        }
    </script>
</x-app-layout>
