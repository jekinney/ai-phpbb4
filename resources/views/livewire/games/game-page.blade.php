<div class="max-w-4xl mx-auto p-6">
    {{-- Game Header --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <span class="text-3xl">{{ $game->icon }}</span>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $game->name }}</h1>
                    <p class="text-gray-600">{{ $game->description }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-blue-600">{{ $score }}</div>
                <div class="text-sm text-gray-500">Current Score</div>
                <div class="text-xs text-gray-400">Game: {{ $game->slug }}</div>
                <div class="text-xs text-gray-400">State: {{ $gameState }}</div>
            </div>
        </div>

        {{-- Game Controls --}}
        <div class="flex items-center justify-center space-x-4 mb-4">
            @if($gameState === 'ready')
                <button wire:click="startGame" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                    Start Game
                </button>
            @elseif($gameState === 'playing')
                <button wire:click="pauseGame" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">
                    Pause
                </button>
                <button wire:click="resetGame" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    Reset
                </button>
            @elseif($gameState === 'paused')
                <button wire:click="pauseGame" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Resume
                </button>
                <button wire:click="resetGame" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    Reset
                </button>
            @elseif($gameState === 'game_over')
                <button wire:click="resetGame" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                    Play Again
                </button>
                @auth
                    <button wire:click="submitScore" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Save Score
                    </button>
                @endauth
            @endif
        </div>

        {{-- Guest Notice --}}
        @guest
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Playing as Guest
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>You can play this game, but your scores won't be saved. <a href="{{ route('login') }}" class="font-medium underline">Login</a> or <a href="{{ route('register') }}" class="font-medium underline">register</a> to save your progress!</p>
                        </div>
                    </div>
                </div>
            </div>
        @endguest
    </div>

    {{-- Game Area --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        @if($game->slug === 'snake')
            @include('livewire.games.partials.snake-game-area')
        @elseif($game->slug === 'memory-match')
            @include('livewire.games.partials.memory-match-game-area')
        @elseif($game->slug === 'tetris')
            @include('livewire.games.partials.tetris-game-area')
        @elseif($game->slug === '2048')
            @include('livewire.games.partials.2048-game-area')
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">{{ $game->icon }}</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $game->name }}</h3>
                <p class="text-gray-600 mb-4">{{ $game->description }}</p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">This game is coming soon! Stay tuned for updates.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Stats and Leaderboard --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Personal Stats --}}
        @auth
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Personal Best:</span>
                        <span class="font-semibold">{{ $personalBest }}</span>
                    </div>
                    @if($currentRank)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Current Rank:</span>
                            <span class="font-semibold">#{{ $currentRank }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Scoring Type:</span>
                        <span class="font-semibold capitalize">{{ $game->scoring_type }} Score Wins</span>
                    </div>
                </div>
            </div>
        @endauth

        {{-- Leaderboard --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Leaderboard</h3>
            @if($topScores->count() > 0)
                <div class="space-y-2">
                    @foreach($topScores->take(5) as $index => $score)
                        <div class="flex items-center justify-between py-2 px-3 rounded {{ $loop->first ? 'bg-yellow-50' : ($loop->iteration <= 3 ? 'bg-gray-50' : '') }}">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm font-semibold w-6">
                                    @if($loop->first) 🥇
                                    @elseif($loop->iteration === 2) 🥈
                                    @elseif($loop->iteration === 3) 🥉
                                    @else {{ $loop->iteration }}
                                    @endif
                                </span>
                                <span class="text-sm">{{ $score->user->name ?? 'Anonymous' }}</span>
                            </div>
                            <span class="text-sm font-semibold">{{ $score->score }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No scores yet. Be the first to play!</p>
            @endif
        </div>
    </div>
</div>
