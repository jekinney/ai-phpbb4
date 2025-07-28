<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Messages</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your personal messages</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button 
                wire:click="switchTab('inbox')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'inbox' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Inbox
                @if($stats['total'] > 0)
                    <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">{{ $stats['total'] }}</span>
                @endif
            </button>
            <button 
                wire:click="switchTab('unread')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'unread' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Unread
                @if($stats['unread'] > 0)
                    <span class="ml-2 bg-red-100 text-red-900 py-0.5 px-2.5 rounded-full text-xs">{{ $stats['unread'] }}</span>
                @endif
            </button>
            <button 
                wire:click="switchTab('sent')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'sent' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Sent
                @if($stats['sent'] > 0)
                    <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">{{ $stats['sent'] }}</span>
                @endif
            </button>
        </nav>
    </div>

    <!-- Actions Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search -->
        <div class="flex-1 max-w-md">
            <input 
                type="search" 
                wire:model.live.debounce.300ms="search"
                placeholder="Search messages..."
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-3">
            @if(!empty($selectedMessages))
                <button 
                    wire:click="markSelectedAsRead"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Mark Read
                </button>
                <button 
                    wire:click="deleteSelected"
                    class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            @endif
            @if(!auth()->user()->isPmBanned())
                <a href="{{ route('messages.compose') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Compose
                </a>
            @else
                <div class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636"></path>
                    </svg>
                    Messaging Restricted
                </div>
            @endif
        </div>
    </div>

    <!-- Messages List -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
        @if($messages->count() > 0)
            <!-- Select All -->
            <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Select All</span>
                </label>
            </div>

            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($messages as $message)
                    <li class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="px-4 py-4 flex items-center justify-between">
                            <div class="flex items-center min-w-0 flex-1">
                                <!-- Checkbox -->
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selectedMessages" 
                                    value="{{ $message->id }}"
                                    class="mr-4 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <!-- Message Info -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <!-- Read Status -->
                                            @if(!$message->isReadBy(auth()->user()))
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            @else
                                                <div class="w-2 h-2"></div>
                                            @endif

                                            <!-- Sender/Recipient -->
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                @if($activeTab === 'sent')
                                                    To: {{ $message->recipients->pluck('user.name')->join(', ') }}
                                                @else
                                                    {{ $message->sender->name }}
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Date -->
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $message->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <!-- Subject -->
                                    <div class="mt-1">
                                        <a href="{{ route('messages.show', $message) }}" 
                                           class="text-sm {{ !$message->isReadBy(auth()->user()) ? 'font-semibold' : '' }} text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $message->subject }}
                                        </a>
                                    </div>

                                    <!-- Preview -->
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ Str::limit($message->content, 100) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                @if(!$message->isReadBy(auth()->user()))
                                    <button 
                                        wire:click="markAsRead({{ $message->id }})"
                                        class="text-gray-400 hover:text-blue-600" 
                                        title="Mark as read">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                @endif
                                <button 
                                    wire:click="deleteMessage({{ $message->id }})"
                                    class="text-gray-400 hover:text-red-600" 
                                    title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $messages->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4-4-4m0 0l4 4 4-4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No messages</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($activeTab === 'sent')
                        You haven't sent any messages yet.
                    @elseif($activeTab === 'unread')
                        You have no unread messages.
                    @else
                        You have no messages in your inbox.
                    @endif
                </p>
                @if($activeTab !== 'sent' && !auth()->user()->isPmBanned())
                    <div class="mt-6">
                        <a href="{{ route('messages.compose') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Send your first message
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
