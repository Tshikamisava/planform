<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Planform') }} - Register</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
                    <p class="mt-2 text-sm text-gray-600">Get started with your free account</p>
                </div>

                <!-- Social Login Buttons -->
                <div class="space-y-3">
                    <button type="button" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.24 10.285V14.4h6.806c-.272 1.765-2.059 4.2-6.806 4.2-4.093 0-7.439-3.334-7.439-7.439s3.346-7.439 7.439-7.439c1.828 0 3.46.628 4.743 1.648l3.331-3.331C19.425 2.832 16.23 1.5 12.24 1.5 5.785 1.5.5 6.785.5 12.24s5.285 10.74 11.74 10.74z"/>
                            <path fill="#4285F4" d="M5.838 14.741l-1.204-1.204C3.993 12.896 3.5 11.621 3.5 10.24c0-1.381.493-2.656 1.134-3.297l1.204 1.204c.372.372.372.976 0 1.348-.372.372-.976 0-1.348z"/>
                            <path fill="#34A853" d="M12.24 22.5c2.99 0 5.58-1.041 7.395-2.795l-3.331-3.331c-.928.628-2.019.996-3.395.996-4.747 0-2.765-2.059-4.2-6.806-4.2v4.115z"/>
                            <path fill="#FBBC05" d="M5.838 7.739l-1.204-1.204C3.993 7.584 3.5 6.309 3.5 4.928c0-1.381.493-2.656 1.134-3.297l1.204 1.204c.372.372.372.976 0 1.348-.372.372-.976 0-1.348z"/>
                            <path fill="#EA4335" d="M12.24 1.5c2.99 0 5.58 1.041 7.395 2.795L16.304 1.38C14.489.626 11.9 0 8.91 0 5.785 0 .5 6.785.5 12.24s5.285 10.74 11.74 10.74z"/>
                        </svg>
                        Continue with Google
                    </button>
                    
                    <button type="button" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Continue with Facebook
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-50 text-gray-500">Or continue with</span>
                    </div>
                </div>

                <!-- Register Form -->
                <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
                        <input id="name" name="name" type="text" autocomplete="name" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms and Privacy -->
                    <div class="flex items-center">
                        <input id="agree_terms" name="agree_terms" type="checkbox"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="agree_terms" class="ml-2 block text-sm text-gray-900">
                            By creating an account, you agree to our 
                            <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> 
                            and 
                            <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create account
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <span class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                Sign in
                            </a>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <x-toast />
    </body>
</html>
