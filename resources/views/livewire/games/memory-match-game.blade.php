<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Game Header -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 flex items-center justify-center">
                <span class="text-4xl mr-3">🧠</span>
                Memory Match
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Match pairs of cards to clear the board. Test your memory!</p>
        </div>

    <!-- Game Stats -->
    <div class="flex justify-center space-x-6 mb-6">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $score }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Score</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $moves }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Moves</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $matches }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Matches</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="timer-display">{{ $timer }}s</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Time</div>
        </div>
        @auth
            @if($personalBest > 0)
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $personalBest }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Personal Best</div>
                </div>
            @endif
            @if($currentRank)
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">#{{ $currentRank }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Your Rank</div>
                </div>
            @endif
        @else
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-400 dark:text-gray-500">--</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Login for Stats</div>
            </div>
        @endauth
    </div>

    <!-- Game Board Container -->
    <div class="flex justify-center mb-6">
        <div class="relative">
            <!-- Game Board -->
            <div class="grid grid-cols-{{ $gridSize }} gap-3 p-6 bg-gray-100 dark:bg-gray-800 rounded-lg border-4 border-gray-300 dark:border-gray-600">
                @foreach($cards as $card)
                    <div wire:click="flipCard({{ $card['id'] }})" 
                         class="w-20 h-20 rounded-lg cursor-pointer transition-all duration-300 transform hover:scale-105 {{ $card['flipped'] || $card['matched'] ? 'rotate-y-180' : '' }}"
                         style="perspective: 1000px;">
                        
                        <div class="relative w-full h-full transition-transform duration-500 transform-style-preserve-3d {{ $card['flipped'] || $card['matched'] ? 'rotate-y-180' : '' }}">
                            <!-- Card Back -->
                            <div class="absolute inset-0 w-full h-full rounded-lg backface-hidden 
                                @if($card['matched']) 
                                    bg-green-200 border-green-400 
                                @else 
                                    bg-gradient-to-br from-blue-400 to-purple-500 hover:from-blue-500 hover:to-purple-600 
                                @endif
                                border-2 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                @if($card['matched'])
                                    ✓
                                @else
                                    ?
                                @endif
                            </div>
                            
                            <!-- Card Front -->
                            <div class="absolute inset-0 w-full h-full rounded-lg backface-hidden rotate-y-180
                                @if($card['matched']) 
                                    bg-green-100 border-green-400 
                                @else 
                                    bg-white border-gray-300 
                                @endif
                                border-2 flex items-center justify-center text-4xl shadow-lg">
                                {{ $card['symbol'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Game State Overlay -->
            @if($gameState !== 'playing')
                <div class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center rounded-lg">
                    <div class="text-center text-white">
                        @if($gameState === 'ready')
                            <h2 class="text-3xl font-bold mb-4">Ready to Play?</h2>
                            <p class="mb-6">Click cards to flip them and find matching pairs</p>
                            <button wire:click="startGame" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                Start Game
                            </button>
                        @elseif($gameState === 'game_over')
                            <h2 class="text-3xl font-bold mb-4">Congratulations! 🎉</h2>
                            <div class="mb-4">
                                <p class="text-xl mb-2">Final Score: {{ $score }}</p>
                                <p class="text-lg mb-1">Moves: {{ $moves }}</p>
                                <p class="text-lg mb-1">Time: {{ $timer }} seconds</p>
                                @auth
                                    @if($score > $personalBest)
                                        <p class="text-green-400 mb-4">🏆 New Personal Best!</p>
                                    @endif
                                @else
                                    <p class="text-yellow-400 mb-4">💡 Login to save your score and compete on the leaderboard!</p>
                                @endauth
                            </div>
                            <div class="space-x-4">
                                <button wire:click="restartGame" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                    Play Again
                                </button>
                                @auth
                                    @if($score > 0 && $score <= $personalBest)
                                        <button wire:click="submitScore" 
                                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                                            Submit Score
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                                        Login to Save Score
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Game Controls -->
    <div class="text-center space-x-4">
        @if($gameState === 'playing')
            <button wire:click="restartGame" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Restart Game
            </button>
        @endif
        
        <a href="{{ route('games.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Games
        </a>
    </div>

    <!-- Instructions -->
    <div class="mt-8 bg-gray-100 dark:bg-gray-800 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">How to Play</h3>
        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
            <li>• Click on cards to flip them over and reveal the symbol</li>
            <li>• Find matching pairs by remembering where each symbol is located</li>
            <li>• Matched pairs will stay flipped and turn green</li>
            <li>• Complete the game by matching all pairs</li>
            <li>• Score points for each match, with bonus points for fewer moves</li>
            <li>• Finish quickly for a time bonus!</li>
        </ul>
    </div>
</div>

@script
<script>
    let gameTimer;
    let timerStarted = false;

    // Listen for timer events
    $wire.on('startTimer', () => {
        startTimer();
    });

    $wire.on('stopTimer', () => {
        stopTimer();
    });

    $wire.on('flipCardsBack', (data) => {
        setTimeout(() => {
            $wire.flipCardsBack(data[0].cardIds);
        }, data[0].delay);
    });

    function startTimer() {
        if (timerStarted) return;
        
        timerStarted = true;
        let seconds = 0;
        
        gameTimer = setInterval(() => {
            seconds++;
            document.getElementById('timer-display').textContent = seconds + 's';
            $wire.updateTimer(seconds);
        }, 1000);
    }

    function stopTimer() {
        timerStarted = false;
        if (gameTimer) {
            clearInterval(gameTimer);
            gameTimer = null;
        }
    }

    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        stopTimer();
    });
</script>
@endscript

<style>
    .backface-hidden {
        backface-visibility: hidden;
    }
    
    .rotate-y-180 {
        transform: rotateY(180deg);
    }
    
    .transform-style-preserve-3d {
        transform-style: preserve-3d;
    }
</style>
</div>
