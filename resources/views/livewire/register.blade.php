<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-auto flex items-center justify-center">
                <h1 class="text-2xl font-bold text-gray-900">AI-phpBB4</h1>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500" wire:navigate>
                    sign in to your existing account
                </a>
            </p>
        </div>
        <form wire:submit="register" class="mt-8 space-y-6">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        wire:model="name"
                        autocomplete="name" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border placeholder-gray-400 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}" 
                        placeholder="Enter your full name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        wire:model="email"
                        autocomplete="email" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border placeholder-gray-400 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}" 
                        placeholder="Enter your email address">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        wire:model="password"
                        autocomplete="new-password" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border placeholder-gray-400 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ $errors->has('password') ? 'border-red-300' : 'border-gray-300' }}" 
                        placeholder="Create a password (minimum 8 characters)">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        wire:model="password_confirmation"
                        autocomplete="new-password" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border placeholder-gray-400 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ $errors->has('password_confirmation') ? 'border-red-300' : 'border-gray-300' }}" 
                        placeholder="Confirm your password">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            wire:model="terms"
                            required
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-700">
                            I agree to the 
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Terms of Service</a>
                            and 
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Privacy Policy</a>
                        </label>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400 absolute left-3 inset-y-0 my-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <span wire:loading>
                        <svg class="animate-spin h-5 w-5 text-white absolute left-3 inset-y-0 my-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove class="ml-6">Create account</span>
                    <span wire:loading class="ml-6">Creating account...</span>
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('home') }}" class="font-medium text-blue-600 hover:text-blue-500" wire:navigate>
                    ← Back to home
                </a>
            </div>
        </form>
    </div>
</div>
