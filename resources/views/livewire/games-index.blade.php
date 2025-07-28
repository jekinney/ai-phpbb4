<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Games</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Challenge yourself and compete with other players</p>
        @guest
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            You can play all games as a guest, but your scores won't be saved or count towards leaderboards. 
                            <a href="{{ route('login') }}" class="font-medium underline hover:text-blue-600 dark:hover:text-blue-200">Login</a> 
                            or 
                            <a href="{{ route('register') }}" class="font-medium underline hover:text-blue-600 dark:hover:text-blue-200">register</a> 
                            to save your progress and compete!
                        </p>
                    </div>
                </div>
            </div>
        @endguest
    </div>

    <!-- Games Grid -->
    @if($games->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($games as $game)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            @if($game->icon)
                                <div class="text-4xl mr-4">{{ $game->icon }}</div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $game->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ $game->scoring_type }} score wins</p>
                            </div>
                        </div>
                        
                        @if($game->description)
                            <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $game->description }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-medium">{{ $game->leaderboard()->count() }}</span> players
                            </div>
                            @if($game->reset_frequency !== 'never')
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Resets {{ $game->reset_frequency }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Top 3 Players -->
                        @php
                            $topPlayers = $game->topPlayers()->take(3);
                        @endphp
                        @if($topPlayers->count() > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Top Players</h4>
                                <div class="space-y-1">
                                    @foreach($topPlayers as $entry)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center">
                                                <span class="w-4 h-4 rounded-full bg-yellow-400 text-xs font-bold text-white flex items-center justify-center mr-2">
                                                    {{ $entry->rank }}
                                                </span>
                                                <span class="text-gray-900 dark:text-gray-100">{{ $entry->user->name }}</span>
                                            </div>
                                            <span class="font-medium text-gray-600 dark:text-gray-400">
                                                @if($game->scoring_type === 'time' && $entry->best_time)
                                                    {{ $entry->formatted_time }}
                                                @else
                                                    {{ $entry->formatted_score }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex space-x-3">
                            @if($game->slug === 'snake')
                                <a href="{{ route('games.snake') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 text-center">
                                    Play Game
                                </a>
                            @elseif($game->slug === 'memory-match')
                                <a href="{{ route('games.memory-match') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 text-center">
                                    Play Game
                                </a>
                            @else
                                <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                    Play Game
                                </button>
                            @endif
                            <button class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Leaderboard
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No games available</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                No games are currently active. Check back later!
            </p>
        </div>
    @endif
</div>
