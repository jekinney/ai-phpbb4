<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $message->subject }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    From: {{ $message->sender->name }} • {{ $message->created_at->format('M j, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @if($this->canReply)
                    <button 
                        wire:click="toggleReplyForm"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Reply
                    </button>
                @endif
                
                @if($this->canDelete)
                    <button 
                        wire:click="deleteMessage"
                        wire:confirm="Are you sure you want to delete this message?"
                        class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                @endif
            </div>
        </div>
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

    <!-- Message Content -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <!-- Message Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img class="h-10 w-10 rounded-full" 
                             src="https://ui-avatars.com/api/?name={{ urlencode($message->sender->name) }}&color=7F9CF5&background=EBF4FF" 
                             alt="{{ $message->sender->name }}">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $message->sender->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    <!-- Read Status -->
                    @if(!$this->isRead)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Unread
                        </span>
                    @endif
                </div>

                <!-- Recipients -->
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    <strong>To:</strong> 
                    {{ $message->recipients->pluck('user.name')->join(', ') }}
                </div>
            </div>

            <!-- Message Content -->
            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! nl2br(e($message->content)) !!}
            </div>

            <!-- File Attachments -->
            @if($message->fileAttachments->count() > 0)
                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Attachments</h4>
                    <div class="space-y-2">
                        @foreach($message->fileAttachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    @if($attachment->is_image)
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attachment->file_size }} bytes</p>
                                    </div>
                                </div>
                                <a href="{{ route('files.download', $attachment) }}" 
                                   class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reply Form -->
    @if($showReplyForm)
        <div class="mt-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Reply to this message</h3>
                
                <form wire:submit="sendReply" class="space-y-4">
                    <div>
                        <label for="replyContent" class="sr-only">Reply content</label>
                        <textarea 
                            id="replyContent"
                            wire:model="replyContent"
                            rows="4"
                            placeholder="Type your reply here..."
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('replyContent')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between">
                        <div class="flex space-x-3">
                            <button 
                                type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled">
                                <svg wire:loading wire:target="sendReply" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="sendReply">Send Reply</span>
                                <span wire:loading wire:target="sendReply">Sending...</span>
                            </button>
                        </div>

                        <button 
                            type="button"
                            wire:click="toggleReplyForm"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
