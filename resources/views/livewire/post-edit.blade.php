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
                            @if(!$isEditing)
                                <button wire:click="quotePost" 
                                       class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center"
                                       title="Quote this post">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Quote
                                </button>
                            @endif
                            
                            @can('update', $post)
                                @if(!$isEditing)
                                    <button wire:click="startEditing" 
                                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                        Edit
                                    </button>
                                @endif
                            @endcan
                            
                            @can('delete', $post)
                                @if(!$post->is_first_post && !$isEditing)
                                    <button wire:click="deletePost"
                                            onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.')"
                                            class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                        Delete
                                    </button>
                                @endif
                            @endcan
                        </div>
                    @endauth
                </div>

                <!-- Post Content or Edit Form -->
                @if($isEditing)
                    <!-- Edit Form -->
                    <div class="space-y-4">
                        <div>
                            <label for="content-{{ $post->id }}" class="sr-only">Edit post content</label>
                            
                            <!-- Rich Text Editor -->
                            <livewire:rich-text-editor 
                                :content="$content"
                                editor-id="edit-{{ $post->id }}"
                                placeholder="Edit your post content..."
                                :height="250"
                                wire:key="edit-editor-{{ $post->id }}-{{ $isEditing ? 'active' : 'inactive' }}"
                            />
                            
                            @error('content')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <button wire:click="savePost"
                                    class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Save Changes
                            </button>
                            <button wire:click="cancelEdit"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Regular Post Content -->
                    <div class="prose prose-sm max-w-none dark:prose-invert">
                        {!! $post->content_html ?: nl2br(e($post->content)) !!}
                    </div>
                @endif

                <!-- Edit History -->
                @if($post->wasEdited() && !$isEditing)
                    <div class="mt-4 pt-3 border-t border-neutral-200 dark:border-neutral-700 text-xs text-gray-500 dark:text-gray-500">
                        Last edited by {{ $post->editedBy->name }} on {{ $post->edited_at->format('M j, Y \a	 g:i A') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
