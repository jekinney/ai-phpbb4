<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ $replyTo ? 'Reply to Message' : 'Compose Message' }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Send a personal message to another user</p>
    </div>

    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('messages.inbox') }}" 
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Messages
        </a>
    </div>

    <form wire:submit="send" class="space-y-6">
        <!-- Error Display -->
        @if ($errors->has('general'))
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ $errors->first('general') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <!-- Recipients -->
                <div class="relative">
                    <label for="recipients" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Recipients <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative">
                        <input 
                            type="text" 
                            id="recipients"
                            wire:model.live.debounce.300ms="recipients"
                            placeholder="Type username(s), separated by commas"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            autocomplete="off">
                        
                        <!-- Suggestions Dropdown -->
                        @if($showSuggestions && count($recipientSuggestions) > 0)
                            <div class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @foreach($recipientSuggestions as $suggestion)
                                    <button 
                                        type="button"
                                        wire:click="selectRecipient({{ $suggestion->id }})"
                                        class="w-full text-left relative cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-blue-600 hover:text-white focus:bg-blue-600 focus:text-white">
                                        <span class="block truncate">{{ $suggestion->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @error('recipients')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Enter usernames separated by commas. Start typing to see suggestions.
                    </p>
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input 
                            type="text" 
                            id="subject"
                            wire:model="subject"
                            placeholder="Enter message subject"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('subject')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <textarea 
                            id="content"
                            wire:model="content"
                            rows="8"
                            placeholder="Type your message here..."
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-between">
            <div class="flex space-x-3">
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <svg wire:loading wire:target="send" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="send">Send Message</span>
                    <span wire:loading wire:target="send">Sending...</span>
                </button>

                <button 
                    type="button"
                    wire:click="saveDraft"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <svg wire:loading wire:target="saveDraft" class="animate-spin -ml-1 mr-3 h-4 w-4 text-gray-700" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="saveDraft">Save Draft</span>
                    <span wire:loading wire:target="saveDraft">Saving...</span>
                </button>
            </div>

            <a href="{{ route('messages.inbox') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </a>
        </div>
    </form>
</div>
