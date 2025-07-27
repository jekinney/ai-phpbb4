<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-white">
    <div class="min-h-full">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <h1 class="text-xl font-bold text-gray-900">AI-phpBB4</h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                            @can('access_admin_panel')
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">Admin</a>
                            @endcan
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main>
            <!-- Hero Section -->
            <div class="relative bg-gray-900">
                <div class="absolute inset-0">
                    <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2850&q=80" alt="">
                    <div class="absolute inset-0 bg-gray-900 opacity-60"></div>
                </div>
                <div class="relative mx-auto max-w-7xl py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $heroTitle }}</h1>
                    <p class="mt-6 max-w-3xl text-xl text-gray-300">{{ $heroSubtitle }}</p>
                    
                    <!-- Search Bar -->
                    <div class="mt-10 max-w-md">
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input 
                                wire:model.live="searchQuery" 
                                type="text" 
                                class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
                                placeholder="Search forums and categories..."
                            >
                            @if($showSearchResults && count($searchResults) > 0)
                                <div class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-200">
                                    @foreach($searchResults as $result)
                                        <div class="px-4 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                            <div class="font-medium text-gray-900">{{ $result['title'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $result['description'] }} • {{ ucfirst($result['type']) }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if($showSearchResults)
                            <button wire:click="clearSearch" class="mt-2 text-sm text-gray-300 hover:text-white">Clear search</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="bg-white py-12">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_users']) }}</div>
                            <div class="text-sm text-gray-600">Total Members</div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['online_users']) }}</div>
                            <div class="text-sm text-gray-600">Online Now</div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_roles']) }}</div>
                            <div class="text-sm text-gray-600">User Roles</div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_permissions']) }}</div>
                            <div class="text-sm text-gray-600">Permissions</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News Section -->
            @if($newsArticles->count() > 0)
            <div class="bg-gray-50 py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Latest News</h2>
                        <p class="mt-4 text-lg leading-6 text-gray-600">Stay updated with the latest community developments</p>
                    </div>
                    <div class="mt-12 grid gap-8 lg:grid-cols-3">
                        @foreach($newsArticles as $article)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $article->title }}</h3>
                                    <p class="text-gray-600">{{ $article->content }}</p>
                                    <div class="mt-4 text-sm text-gray-500">
                                        {{ $article->created_at->format('M j, Y') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- CTA Section -->
            <div class="bg-indigo-600">
                <div class="mx-auto max-w-7xl py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $ctaTitle }}</h2>
                        <p class="mt-4 text-lg leading-6 text-indigo-200">{{ $ctaSubtitle }}</p>
                        <div class="mt-8">
                            @guest
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-white px-6 py-3 text-base font-medium text-indigo-600 shadow-sm hover:bg-indigo-50">
                                    Get Started
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md bg-white px-6 py-3 text-base font-medium text-indigo-600 shadow-sm hover:bg-indigo-50">
                                    Go to Dashboard
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900">
            <div class="mx-auto max-w-7xl py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} AI-phpBB4. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
