<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Static Pages Management</h1>
                <p class="text-gray-600">Manage your website's static pages like About Us, Contact, etc.</p>
            </div>
            <button 
                wire:click="createPage" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium"
            >
                Create New Page
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search -->
    <div class="mb-4">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search pages..." 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
    </div>

    <!-- Pages Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($pages as $page)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ $page->title }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            /{{ $page->slug }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <!-- Status badges -->
                                        @if($page->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Inactive
                                            </span>
                                        @endif

                                        @if($page->show_in_footer)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Footer
                                            </span>
                                        @endif

                                        <span class="text-xs text-gray-500">
                                            Order: {{ $page->sort_order }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($page->meta_description)
                                    <p class="mt-2 text-sm text-gray-600">
                                        {{ Str::limit($page->meta_description, 100) }}
                                    </p>
                                @endif
                                
                                <div class="mt-2 text-xs text-gray-500">
                                    Created: {{ $page->created_at->format('M j, Y') }} by {{ $page->creator->name }}
                                    @if($page->updated_at != $page->created_at && $page->updater)
                                        | Updated: {{ $page->updated_at->format('M j, Y') }} by {{ $page->updater->name }}
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-4 flex space-x-2">
                                <!-- Toggle Active -->
                                <button 
                                    wire:click="toggleActive({{ $page->id }})"
                                    class="text-sm {{ $page->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}"
                                >
                                    {{ $page->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                
                                <!-- Edit -->
                                <button 
                                    wire:click="editPage({{ $page->id }})"
                                    class="text-sm text-indigo-600 hover:text-indigo-800"
                                >
                                    Edit
                                </button>
                                
                                <!-- Delete -->
                                <button 
                                    wire:click="deletePage({{ $page->id }})"
                                    onclick="return confirm('Are you sure you want to delete this page?')"
                                    class="text-sm text-red-600 hover:text-red-800"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    No pages found. <button wire:click="createPage" class="text-indigo-600 hover:text-indigo-800">Create your first page</button>
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $pages->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editingPage ? 'Edit Page' : 'Create New Page' }}
                    </h3>
                    
                    <form wire:submit="savePage">
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                wire:model="title" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Slug -->
                        <div class="mb-4">
                            <label for="slug" class="block text-sm font-medium text-gray-700">URL Slug</label>
                            <input 
                                type="text" 
                                id="slug" 
                                wire:model="slug" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            <p class="mt-1 text-sm text-gray-500">URL will be: /pages/{{ $slug ?: 'your-slug' }}</p>
                            @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-4">
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <textarea 
                                id="meta_description" 
                                wire:model="meta_description" 
                                rows="2"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Brief description for SEO (optional)"
                            ></textarea>
                            @error('meta_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea 
                                id="content" 
                                wire:model="content" 
                                rows="10"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Page content (HTML allowed)"
                            ></textarea>
                            @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Settings Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Active -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>

                            <!-- Show in Footer -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="show_in_footer" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Show in Footer</span>
                                </label>
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                                <input 
                                    type="number" 
                                    id="sort_order" 
                                    wire:model="sort_order" 
                                    min="0"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                @error('sort_order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button 
                                type="button" 
                                wire:click="closeModal"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                {{ $editingPage ? 'Update Page' : 'Create Page' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
