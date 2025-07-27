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

        <!-- Posts -->
        @foreach($posts as $post)
            <div class="border-b border-neutral-100 dark:border-neutral-800 last:border-b-0" id="post-{{ $post->id }}">
                <div class="p-6">
                    <div class="flex">
                        <!-- User Info Sidebar -->
                        <div class="flex-shrink-0 w-40 mr-6">
                            <div class="text-center">
                                <!-- User Avatar -->
                                <div class="w-16 h-16 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mb-2 flex items-center justify-center">
                                    <span class="text-lg font-semibold text-gray-600 dark:text-gray-300">
                                        {{ $post->user->initials() }}
                                    </span>
                                </div>
                                
                                <!-- Username -->
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $post->user->name }}
                                </div>
                                
                                <!-- User stats -->
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $post->user->posts->count() }} posts
                                </div>
                                
                                <!-- Join date -->
                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    Joined {{ $post->user->created_at->format('M Y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Post Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <a href="#post-{{ $post->id }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">
                                        {{ $post->created_at->format('M j, Y \a	 g:i A') }}
                                    </a>
                                    @if($post->is_first_post)
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Original Post
                                        </span>
                                    @endif
                                </div>

                                <!-- Post Actions -->
                                @auth
                                    <div class="flex items-center space-x-2">
                                        @can('update', $post)
                                            <a href="{{ route('posts.edit', $post) }}" 
                                               class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                                Edit
                                            </a>
                                        @endcan
                                        
                                        @can('delete', $post)
                                            @if(!$post->is_first_post)
                                                <button wire:click="$dispatch('confirm-delete-post', { id: {{ $post->id }} })"
                                                        class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                                    Delete
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                @endauth
                            </div>

                            <!-- Post Content -->
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                {!! $post->content_html ?: nl2br(e($post->content)) !!}
                            </div>

                            <!-- Edit History -->
                            @if($post->wasEdited())
                                <div class="mt-4 pt-3 border-t border-neutral-200 dark:border-neutral-700 text-xs text-gray-500 dark:text-gray-500">
                                    Last edited by {{ $post->editedBy->name }} on {{ $post->edited_at->format('M j, Y \a	 g:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
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
</div>
