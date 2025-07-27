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
                <a href="{{ route('forums.show', $forum) }}" 
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                    {{ $forum->category->name }}
                </a>
            </li>
            <li class="text-gray-500">/</li>
            <li class="text-gray-900 dark:text-white font-medium">{{ $forum->name }}</li>
        </ol>
    </nav>

    <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700">
        <!-- Forum Header -->
        <div class="border-b border-neutral-200 dark:border-neutral-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $forum->name }}</h1>
                    @if($forum->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $forum->description }}</p>
                    @endif
                </div>
                
                @auth
                    <a href="{{ route('topics.create', $forum) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        New Topic
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition ease-in-out duration-150">
                        Login to Post
                    </a>
                @endauth
            </div>
        </div>

        <!-- Topics List Header -->
        <div class="bg-neutral-50 dark:bg-neutral-800 px-6 py-3 border-b border-neutral-200 dark:border-neutral-700">
            <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                <div class="col-span-6">Topic</div>
                <div class="col-span-2 text-center">Replies</div>
                <div class="col-span-2 text-center">Views</div>
                <div class="col-span-2 text-center">Last Post</div>
            </div>
        </div>

        <!-- Topics List -->
        @forelse($topics as $topic)
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 last:border-b-0 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                <div class="grid grid-cols-12 gap-4 items-center">
                    <!-- Topic Info -->
                    <div class="col-span-6">
                        <div class="flex items-start space-x-3">
                            <!-- Status Icons -->
                            <div class="flex-shrink-0 mt-1">
                                @if($topic->is_sticky)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Sticky
                                    </span>
                                @endif
                                @if($topic->is_locked)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 ml-1">
                                        Locked
                                    </span>
                                @endif
                            </div>
                            
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-medium">
                                    <a href="{{ route('topics.show', $topic) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                        {{ $topic->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    by <span class="font-medium">{{ $topic->user->name }}</span>
                                    <span class="mx-1">•</span>
                                    {{ $topic->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Replies Count -->
                    <div class="col-span-2 text-center">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ number_format($topic->posts_count - 1) }}
                        </div>
                    </div>

                    <!-- Views Count -->
                    <div class="col-span-2 text-center">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ number_format($topic->views_count) }}
                        </div>
                    </div>

                    <!-- Last Post -->
                    <div class="col-span-2 text-center">
                        @if($topic->lastPost)
                            <div class="text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $topic->lastPostUser->name }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $topic->last_post_at->diffForHumans() }}
                                </div>
                            </div>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-500">—</div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <p class="text-lg font-medium">No topics yet</p>
                    <p class="text-sm mt-1">Be the first to start a discussion!</p>
                    @auth
                        <a href="{{ route('topics.create', $forum) }}" 
                           class="inline-flex items-center px-4 py-2 mt-4 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                            Create First Topic
                        </a>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($topics->hasPages())
        <div class="mt-6">
            {{ $topics->links() }}
        </div>
    @endif

    <!-- Forum Info -->
    <div class="mt-6 bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Forum Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
            <div>
                <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($forum->topics_count) }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Topics</div>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($forum->posts_count) }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Posts</div>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $forum->last_post_at ? $forum->last_post_at->format('M j, Y') : 'Never' }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Last Activity</div>
            </div>
        </div>
    </div>
</div>
