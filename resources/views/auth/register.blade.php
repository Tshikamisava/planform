<x-guest-layout>
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Create your account</h2>
        <p class="mt-2 text-sm text-gray-600">Get started with your free account</p>
    </div>

    <!-- Social Login Buttons -->
    <div class="mt-10">
        <div class="grid grid-cols-2 gap-3">
            <button type="button" class="flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12.24 10.285V14.4h6.806c-.272 1.765-2.059 4.2-6.806 4.2-4.093 0-7.439-3.334-7.439-7.439s3.346-7.439 7.439-7.439c1.828 0 3.46.628 4.743 1.648l3.331-3.331C19.425 2.832 16.23 1.5 12.24 1.5 5.785 1.5.5 6.785.5 12.24s5.285 10.74 11.74 10.74z"/>
                    <path fill="#4285F4" d="M5.838 14.741l-1.204-1.204C3.993 12.896 3.5 11.621 3.5 10.24c0-1.381.493-2.656 1.134-3.297l1.204 1.204c.372.372.372.976 0 1.348-.372.372-.976 0-1.348z"/>
                    <path fill="#34A853" d="M12.24 22.5c2.99 0 5.58-1.041 7.395-2.795l-3.331-3.331c-.928.628-2.019.996-3.395.996-4.747 0-2.765-2.059-4.2-6.806-4.2v4.115z"/>
                    <path fill="#FBBC05" d="M5.838 7.739l-1.204-1.204C3.993 7.584 3.5 6.309 3.5 4.928c0-1.381.493-2.656 1.134-3.297l1.204 1.204c.372.372.372.976 0 1.348-.372.372-.976 0-1.348z"/>
                    <path fill="#EA4335" d="M12.24 1.5c2.99 0 5.58 1.041 7.395 2.795L16.304 1.38C14.489.626 11.9 0 8.91 0 5.785 0 .5 6.785.5 12.24s5.285 10.74 11.74 10.74z"/>
                </svg>
                <span class="text-sm font-semibold leading-6">Google</span>
            </button>
            <button type="button" class="flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent">
                <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-semibold leading-6">Facebook</span>
            </button>
        </div>

        <div class="relative mt-10">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm font-medium leading-6">
                <span class="bg-white px-6 text-gray-900">Or continue with</span>
            </div>
        </div>
    </div>

    <!-- Register Form -->
    <form class="mt-10 space-y-6" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full name</label>
            <div class="mt-2">
                <input id="name" name="name" type="text" autocomplete="name" required
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                       value="{{ old('name') }}">
            </div>
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
            <div class="mt-2">
                <input id="email" name="email" type="email" autocomplete="email" required
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                       value="{{ old('email') }}">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Selection -->
        <div>
            <label for="role" class="block text-sm font-medium leading-6 text-gray-900">Select Role</label>
            <div class="mt-2">
                <select id="role" name="role" required
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    <option value="" disabled selected>Choose your role...</option>
                    <option value="author" {{ old('role') == 'author' ? 'selected' : '' }}>Author (Submit DCR Requests)</option>
                    <option value="recipient" {{ old('role') == 'recipient' ? 'selected' : '' }}>Recipient (Implement Changes)</option>
                    <option value="dom" {{ old('role') == 'dom' ? 'selected' : '' }}>Decision Maker (Approve/Reject)</option>
                    <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Viewer (View Only)</option>
                </select>
            </div>
            @error('role')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
            <div class="mt-2">
                <input id="password" name="password" type="password" autocomplete="new-password" required
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Confirm password</label>
            <div class="mt-2">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms and Privacy -->
        <div class="flex items-center">
            <input id="agree_terms" name="agree_terms" type="checkbox"
                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
            <label for="agree_terms" class="ml-3 block text-sm leading-6 text-gray-900">
                By creating an account, you agree to our 
                <a href="#" class="font-semibold text-blue-600 hover:text-blue-500">Terms of Service</a> 
                and 
                <a href="#" class="font-semibold text-blue-600 hover:text-blue-500">Privacy Policy</a>
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors duration-200">
                Create account
            </button>
        </div>
    </form>

    <p class="mt-10 text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold leading-6 text-blue-600 hover:text-blue-500">
            Sign in
        </a>
    </p>
</x-guest-layout>
