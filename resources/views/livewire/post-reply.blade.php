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
                <div class="mt-6 bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700" id="reply-form">
                    <div class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Post Reply</h3>
                    </div>
                    
                    <form wire:submit="submit" class="p-6">
                        <!-- Quote Preview -->
                        @if ($quotedPostId)
                            <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-500 p-4 rounded-r">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm text-blue-600 dark:text-blue-400 font-semibold mb-2">
                                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Quoting {{ $quotedAuthor }}:
                                        </p>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded border">
                                            {{ Str::limit($quotedContent, 300) }}
                                            @if(strlen($quotedContent) > 300)
                                                <span class="text-gray-500 dark:text-gray-400">...</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" wire:click="clearQuote" 
                                            class="ml-3 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                            title="Remove quote">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Content -->
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Your Reply
                            </label>
                            
                            <!-- Rich Text Editor -->
                            <livewire:rich-text-editor 
                                :content="$content"
                                editor-id="reply-editor"
                                placeholder="Write your reply here..."
                                :height="300"
                                wire:key="reply-editor-{{ $showForm ? 'active' : 'inactive' }}"
                            />
                            
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('scrollToReplyForm', function() {
                setTimeout(() => {
                    const replyForm = document.getElementById('reply-form');
                    if (replyForm) {
                        replyForm.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 100);
            });
        });
    </script>
</div>
