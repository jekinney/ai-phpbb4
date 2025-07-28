<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Documentation Viewer</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Browse and view project documentation files
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="relative">
                    <input 
                        wire:model.live="searchTerm"
                        type="text" 
                        placeholder="Search files..."
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- File List Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Documentation Files
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                            ({{ count($filteredFiles) }} files)
                        </span>
                    </h3>
                    
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @forelse($filteredFiles as $file)
                            <div 
                                wire:click="selectFile('{{ $file['path'] }}')"
                                class="cursor-pointer p-3 rounded-md border {{ $selectedFile === $file['path'] ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }} transition-colors"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $file['name'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $file['relative_path'] }}
                                        </p>
                                    </div>
                                    <div class="ml-2 flex-shrink-0">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $file['extension'] === 'md' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ strtoupper($file['extension']) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $this->getFileSizeHuman($file['size']) }}</span>
                                    <span>{{ date('M j, Y', strtotime($file['modified'])) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No documentation files</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    No documentation files found matching your search.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- File Content Area -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                @if($selectedFile)
                    <!-- File Header -->
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    {{ basename($selectedFile) }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ str_replace(base_path() . DIRECTORY_SEPARATOR, '', $selectedFile) }}
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <button 
                                    wire:click="loadFileContent"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Refresh
                                </button>
                                <a 
                                    href="{{ $selectedFile }}"
                                    download
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- File Content -->
                    <div class="px-4 py-5 sm:p-6">
                        @if(Str::endsWith($selectedFile, '.md'))
                            <!-- Markdown Content -->
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                {!! Str::markdown($fileContent) !!}
                            </div>
                        @else
                            <!-- Plain Text Content -->
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $fileContent }}</pre>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- No File Selected -->
                    <div class="px-4 py-12 sm:px-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Select a documentation file</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Choose a file from the sidebar to view its contents.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
