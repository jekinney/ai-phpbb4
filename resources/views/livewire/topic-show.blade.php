<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="{{ route('forums.index') }}" 
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                    Forums
                </a>
            </li>
            <li class="text-gray-500">/</li>
            <li>
                <a href="{{ route('forums.show', $topic->forum) }}" 
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                    {{ $topic->forum->name }}
                </a>
            </li>
            <li class="text-gray-500">/</li>
            <li class="text-gray-900 dark:text-white font-medium">{{ Str::limit($topic->title, 50) }}</li>
        </ol>
    </nav>

    <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700">
        <!-- Topic Header -->
        <div class="border-b border-neutral-200 dark:border-neutral-700 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $topic->title }}</h1>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>Started by <strong>{{ $topic->user->name }}</strong></span>
                        <span>•</span>
                        <span>{{ $topic->created_at->format('M j, Y \a	 g:i A') }}</span>
                        <span>•</span>
                        <span>{{ number_format($topic->views_count) }} views</span>
                    </div>
                    
                    <!-- Status badges -->
                    <div class="flex items-center space-x-2 mt-3">
                        @if($topic->is_sticky)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Sticky
                            </span>
                        @endif
                        @if($topic->is_locked)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Locked
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Topic Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Follow Component -->
                    <livewire:topic-follow :topic="$topic" />
                    
                    @auth
                        <div class="flex items-center space-x-2">
                            @can('update', $topic)
                                <a href="{{ route('topics.edit', $topic) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Edit
                                </a>
                            @endcan
                            
                            @can('delete', $topic)
                                <button wire:click="$dispatch('confirm-delete')" 
                                        class="inline-flex items-center px-3 py-1 border border-red-300 dark:border-red-600 rounded-md text-sm font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    Delete
                                </button>
                            @endcan
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Posts -->
        @foreach($posts as $post)
            <livewire:post-edit :post="$post" :key="'post-'.$post->id" />
        @endforeach
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @endif

    <!-- Reply Form Component -->
    <livewire:post-reply :topic="$topic" />
    
    <!-- Toast Notifications -->
    <livewire:toast-notification />
</div>
