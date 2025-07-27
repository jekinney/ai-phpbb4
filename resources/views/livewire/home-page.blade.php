<div>
    <!-- Hero Section with Search -->
    <div class="bg-gradient-to-br from-blue-600 to-purple-700">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Welcome to AI-phpBB4
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-xl text-blue-100">
                    A next-generation community platform powered by AI and built with modern Laravel technology. 
                    Connect, discuss, and grow with our vibrant community.
                </p>
                
                <!-- Search Bar -->
                <div class="mx-auto mt-10 max-w-xl relative">
                    <div class="relative">
                        <input 
                            wire:model.live.debounce.300ms="searchQuery"
                            type="text" 
                            placeholder="Search forums, categories, or topics..."
                            class="w-full rounded-lg border-0 bg-white py-4 pl-4 pr-12 text-gray-900 shadow-lg ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            @if($searchQuery)
                                <button 
                                    wire:click="clearSearch"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @else
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    @if($showSearchResults)
                    <div class="absolute z-10 mt-2 w-full rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            @foreach($searchResults as $result)
                            <a href="#" class="group flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                <div class="flex-shrink-0">
                                    @if($result['type'] === 'category')
                                        <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25A1.125 1.125 0 013.75 18.375v-2.25z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="font-medium text-gray-900">{{ $result['title'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $result['description'] }}</div>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 capitalize">
                                        {{ $result['type'] }}
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <dl class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-4">
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-gray-600">Community Members</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{ number_format($stats['total_users']) }}</dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-gray-600">User Roles</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{ $stats['total_roles'] }}</dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-gray-600">Permissions System</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{ $stats['total_permissions'] }}</dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-gray-600">Online Now</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">{{ $stats['online_users'] ?? 0 }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- News and Updates Section -->
    <div class="bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Latest News & Updates</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    Stay informed about the latest developments, features, and community highlights.
                </p>
            </div>
            
            <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                <!-- News Item 1 -->
                <article class="flex flex-col items-start justify-between bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center gap-x-4 text-xs">
                        <time datetime="2025-01-27" class="text-gray-500">Jan 27, 2025</time>
                        <a href="#" class="relative z-10 rounded-full bg-blue-50 px-3 py-1.5 font-medium text-blue-600 hover:bg-blue-100">
                            Platform Update
                        </a>
                    </div>
                    <div class="group relative">
                        <h3 class="mt-3 text-lg font-semibold leading-6 text-gray-900 group-hover:text-gray-600">
                            <a href="#">
                                <span class="absolute inset-0"></span>
                                Advanced ACL System Launched
                            </a>
                        </h3>
                        <p class="mt-5 line-clamp-3 text-sm leading-6 text-gray-600">
                            We've implemented a comprehensive role-based access control system with 34 granular permissions across 6 categories. 
                            This enables fine-tuned control over user capabilities and forum management.
                        </p>
                    </div>
                    <div class="relative mt-8 flex items-center gap-x-4">
                        <img src="https://ui-avatars.com/api/?name=System+Admin&color=7F9CF5&background=EBF4FF" alt="" class="h-10 w-10 rounded-full bg-gray-50">
                        <div class="text-sm leading-6">
                            <p class="font-semibold text-gray-900">
                                <a href="#">
                                    <span class="absolute inset-0"></span>
                                    System Administrator
                                </a>
                            </p>
                            <p class="text-gray-600">Platform Team</p>
                        </div>
                    </div>
                </article>

                <!-- News Item 2 -->
                <article class="flex flex-col items-start justify-between bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center gap-x-4 text-xs">
                        <time datetime="2025-01-25" class="text-gray-500">Jan 25, 2025</time>
                        <a href="#" class="relative z-10 rounded-full bg-green-50 px-3 py-1.5 font-medium text-green-600 hover:bg-green-100">
                            Feature Release
                        </a>
                    </div>
                    <div class="group relative">
                        <h3 class="mt-3 text-lg font-semibold leading-6 text-gray-900 group-hover:text-gray-600">
                            <a href="#">
                                <span class="absolute inset-0"></span>
                                Livewire Admin Dashboard
                            </a>
                        </h3>
                        <p class="mt-5 line-clamp-3 text-sm leading-6 text-gray-600">
                            Experience the new interactive admin dashboard built with Livewire. Real-time user management, 
                            role assignments, and system monitoring without page refreshes.
                        </p>
                    </div>
                    <div class="relative mt-8 flex items-center gap-x-4">
                        <img src="https://ui-avatars.com/api/?name=Dev+Team&color=10B981&background=D1FAE5" alt="" class="h-10 w-10 rounded-full bg-gray-50">
                        <div class="text-sm leading-6">
                            <p class="font-semibold text-gray-900">
                                <a href="#">
                                    <span class="absolute inset-0"></span>
                                    Development Team
                                </a>
                            </p>
                            <p class="text-gray-600">Engineering</p>
                        </div>
                    </div>
                </article>

                <!-- News Item 3 -->
                <article class="flex flex-col items-start justify-between bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center gap-x-4 text-xs">
                        <time datetime="2025-01-20" class="text-gray-500">Jan 20, 2025</time>
                        <a href="#" class="relative z-10 rounded-full bg-purple-50 px-3 py-1.5 font-medium text-purple-600 hover:bg-purple-100">
                            Community
                        </a>
                    </div>
                    <div class="group relative">
                        <h3 class="mt-3 text-lg font-semibold leading-6 text-gray-900 group-hover:text-gray-600">
                            <a href="#">
                                <span class="absolute inset-0"></span>
                                Welcome to AI-phpBB4 Beta
                            </a>
                        </h3>
                        <p class="mt-5 line-clamp-3 text-sm leading-6 text-gray-600">
                            Join our growing community of developers, enthusiasts, and contributors. 
                            Built on Laravel 12 with modern technologies like Livewire, Alpine.js, and Tailwind CSS.
                        </p>
                    </div>
                    <div class="relative mt-8 flex items-center gap-x-4">
                        <img src="https://ui-avatars.com/api/?name=Community+Manager&color=8B5CF6&background=EDE9FE" alt="" class="h-10 w-10 rounded-full bg-gray-50">
                        <div class="text-sm leading-6">
                            <p class="font-semibold text-gray-900">
                                <a href="#">
                                    <span class="absolute inset-0"></span>
                                    Community Manager
                                </a>
                            </p>
                            <p class="text-gray-600">Community Team</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <!-- Call to Action Section -->
    <div class="bg-blue-600">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                    Ready to join our community?
                </h2>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-blue-100">
                    Connect with like-minded individuals, share your knowledge, and be part of the next generation forum platform.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    @guest
                        <a href="{{ route('register') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-blue-600 shadow-sm hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Get started
                        </a>
                        <a href="{{ route('forums.index') }}" class="text-sm font-semibold leading-6 text-white">
                            Browse forums <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a href="{{ route('forums.index') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-blue-600 shadow-sm hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Browse Forums
                        </a>
                        <a href="#" class="text-sm font-semibold leading-6 text-white">
                            Create a topic <span aria-hidden="true">→</span>
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
