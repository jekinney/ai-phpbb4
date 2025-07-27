<div>
    @auth
        @if(!$topic->is_locked)
            <!-- Reply Button -->
            @if(!$showForm)
                <div class="mt-6 text-center">
                    <button wire:click="toggleForm" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Post Reply
                    </button>
                </div>
            @endif

            <!-- Reply Form -->
            @if($showForm)
                <div class="mt-6 bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700">
                    <div class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Post Reply</h3>
                    </div>
                    
                    <form wire:submit="submit" class="p-6">
                        <!-- Content -->
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Your Reply
                            </label>
                            <textarea wire:model="content"
                                      id="content" 
                                      rows="8"
                                      maxlength="10000"
                                      class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                                      placeholder="Write your reply here..."></textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            @error('locked')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="toggleForm"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </button>
                            
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                <span wire:loading.remove>Post Reply</span>
                                <span wire:loading>Posting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @else
            <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <p class="text-red-800 dark:text-red-200 text-center">
                    This topic is locked and cannot accept new replies.
                </p>
            </div>
        @endif
    @else
        <div class="mt-6 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 text-center">
            <p class="text-gray-600 dark:text-gray-400 mb-4">You must be logged in to reply to this topic.</p>
            <a href="{{ route('login') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                Login to Reply
            </a>
        </div>
    @endauth
</div>
