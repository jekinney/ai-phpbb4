<x-layouts.app title="Create New Topic">
    <div class="max-w-2xl mx-auto">
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
                        {{ $forum->name }}
                    </a>
                </li>
                <li class="text-gray-500">/</li>
                <li class="text-gray-900 dark:text-white font-medium">New Topic</li>
            </ol>
        </nav>

        <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-xs border border-neutral-200 dark:border-neutral-700">
            <!-- Header -->
            <div class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create New Topic</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Starting a new discussion in <strong>{{ $forum->name }}</strong>
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('topics.store', $forum) }}" class="p-6">
                @csrf
                
                <!-- Topic Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Topic Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           required
                           maxlength="255"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                           placeholder="Enter a descriptive title for your topic">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Choose a clear, descriptive title that summarizes your topic
                    </p>
                </div>

                <!-- Post Content -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="12"
                              required
                              maxlength="10000"
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                              placeholder="Write your message here...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Provide detailed information about your topic. Minimum 10 characters required.
                    </p>
                </div>

                <!-- Forum Guidelines -->
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Posting Guidelines</h3>
                    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                        <li>• Be respectful and constructive in your discussions</li>
                        <li>• Search for existing topics before creating a new one</li>
                        <li>• Use clear and descriptive titles</li>
                        <li>• Stay on topic and provide relevant information</li>
                        <li>• Follow the community guidelines and rules</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('forums.show', $forum) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-100 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Create Topic
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Area (Optional enhancement for future) -->
        <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Make sure to review your post before submitting. You can edit it later if needed.</p>
        </div>
    </div>
</x-layouts.app>
