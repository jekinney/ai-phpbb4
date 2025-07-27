<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700">
        <!-- Header -->
        <div class="border-b border-neutral-200 dark:border-neutral-700 p-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Forums</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Welcome to our community discussion board</p>
        </div>

        <!-- Categories -->
        @forelse($categories as $category)
            <div class="border-b border-neutral-200 dark:border-neutral-700 last:border-b-0">
                <!-- Category Header -->
                <div class="bg-neutral-50 dark:bg-neutral-800 px-6 py-3">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $category->name }}
                    </h2>
                    @if($category->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $category->description }}
                        </p>
                    @endif
                </div>

                <!-- Forums in Category -->
                @forelse($category->activeForums as $forum)
                    <div class="px-6 py-4 border-b border-neutral-100 dark:border-neutral-800 last:border-b-0 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-base font-medium">
                                    <a href="{{ route('forums.show', $forum) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                        {{ $forum->name }}
                                    </a>
                                </h3>
                                @if($forum->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $forum->description }}
                                    </p>
                                @endif
                            </div>

                            <!-- Forum Stats -->
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($forum->topics_count) }} 
                                    {{ Str::plural('topic', $forum->topics_count) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ number_format($forum->posts_count) }} 
                                    {{ Str::plural('post', $forum->posts_count) }}
                                </div>
                            </div>

                            <!-- Last Post -->
                            @if($forum->lastPost)
                                <div class="ml-6 text-right min-w-0 flex-shrink-0">
                                    <div class="text-sm">
                                        <a href="{{ route('topics.show', $forum->lastPost->topic) }}" 
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors truncate block">
                                            {{ Str::limit($forum->lastPost->topic->title, 25) }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        by {{ $forum->lastPost->user->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $forum->last_post_at->diffForHumans() }}
                                    </div>
                                </div>
                            @else
                                <div class="ml-6 text-right text-sm text-gray-500 dark:text-gray-500">
                                    No posts yet
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        No forums in this category yet.
                    </div>
                @endforelse
            </div>
        @empty
            <div class="p-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <p class="text-lg font-medium">No categories available</p>
                    <p class="text-sm mt-1">Forums will appear here once categories are created.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Forum Statistics -->
    @if($categories->count() > 0)
        <div class="mt-6 bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Forum Statistics</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $categories->sum(fn($cat) => $cat->activeForums->sum('topics_count')) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Topics</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $categories->sum(fn($cat) => $cat->activeForums->sum('posts_count')) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Posts</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $categories->sum(fn($cat) => $cat->activeForums->count()) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Active Forums</div>
                </div>
            </div>
        </div>
    @endif
</div>
