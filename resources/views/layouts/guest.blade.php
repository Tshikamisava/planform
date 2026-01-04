<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Planform') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="h-full antialiased text-gray-900">
        <div class="min-h-screen flex">
            <!-- Left Side: Branding & Decorative -->
            <div class="hidden lg:flex w-1/2 bg-slate-900 relative overflow-hidden flex-col justify-between p-12 text-white">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20 pointer-events-none"></div>
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-sm border border-white/10">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold tracking-tight">Planform</span>
                    </div>
                </div>

                <div class="relative z-10 max-w-lg">
                    <h2 class="text-4xl font-bold mb-6 leading-tight">Manage your platform with confidence.</h2>
                    <p class="text-lg text-slate-300 leading-relaxed">
                        Streamline your workflow, track performance, and make data-driven decisions with our comprehensive management suite.
                    </p>
                </div>

                <div class="relative z-10 text-sm text-slate-400">
                    &copy; {{ date('Y') }} Planform. All rights reserved.
                </div>
            </div>

            <!-- Right Side: Auth Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
                <div class="w-full max-w-md space-y-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
        <x-toast />
    </body>
</html>
