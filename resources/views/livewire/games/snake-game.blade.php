<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Game Header -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 flex items-center justify-center">
            <span class="text-4xl mr-3">🐍</span>
            Snake Game
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Use arrow keys to control the snake. Eat food to grow and score points!</p>
    </div>

    <!-- Game Stats -->
    <div class="flex justify-center space-x-6 mb-6">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $score }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Score</div>
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
            <div id="game-board" 
                 class="grid grid-cols-{{ $boardSize }} gap-0 border-4 border-gray-800 dark:border-gray-600 bg-gray-100 dark:bg-gray-800"
                 style="width: 500px; height: 500px;">
                @for($y = 0; $y < $boardSize; $y++)
                    @for($x = 0; $x < $boardSize; $x++)
                        @php
                            $isSnake = false;
                            $isHead = false;
                            foreach($snake as $index => $segment) {
                                if ($segment['x'] == $x && $segment['y'] == $y) {
                                    $isSnake = true;
                                    $isHead = $index === count($snake) - 1;
                                    break;
                                }
                            }
                            $isFood = $food['x'] == $x && $food['y'] == $y;
                        @endphp
                        <div class="w-full h-full border border-gray-200 dark:border-gray-700
                            @if($isHead) bg-green-600 @elseif($isSnake) bg-green-400 @elseif($isFood) bg-red-500 @else bg-gray-50 dark:bg-gray-900 @endif">
                            @if($isFood)
                                <div class="w-full h-full flex items-center justify-center text-xs">🍎</div>
                            @endif
                        </div>
                    @endfor
                @endfor
            </div>

            <!-- Game State Overlay -->
            @if($gameState !== 'playing')
                <div class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center">
                    <div class="text-center text-white">
                        @if($gameState === 'ready')
                            <h2 class="text-3xl font-bold mb-4">Ready to Play?</h2>
                            <p class="mb-6">Use arrow keys to control the snake</p>
                            <button wire:click="startGame" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                                Start Game
                            </button>
                        @elseif($gameState === 'paused')
                            <h2 class="text-3xl font-bold mb-4">Game Paused</h2>
                            <button wire:click="pauseGame" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                Resume Game
                            </button>
                        @elseif($gameState === 'game_over')
                            <h2 class="text-3xl font-bold mb-4">Game Over!</h2>
                            <p class="text-xl mb-2">Final Score: {{ $score }}</p>
                            @auth
                                @if($score > $personalBest)
                                    <p class="text-green-400 mb-4">🎉 New Personal Best!</p>
                                @endif
                            @else
                                <p class="text-yellow-400 mb-4">💡 Login to save your score and compete on the leaderboard!</p>
                            @endauth
                            <div class="space-x-4">
                                <button wire:click="restartGame" 
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                                    Play Again
                                </button>
                                @auth
                                    @if($score > 0 && $score <= $personalBest)
                                        <button wire:click="submitScore" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                            Submit Score
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
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
            <button wire:click="pauseGame" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Pause
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
            <li>• Use arrow keys (↑ ↓ ← →) to control the snake's direction</li>
            <li>• Eat the red apples to grow longer and increase your score</li>
            <li>• Avoid hitting the walls or your own tail</li>
            <li>• The snake moves faster as you score more points</li>
            <li>• Try to beat your personal best and climb the leaderboard!</li>
        </ul>
    </div>
</div>

@script
<script>
    let gameLoop;
    let gameSpeed = 200;

    // Keyboard controls
    document.addEventListener('keydown', function(event) {
        const keyMap = {
            'ArrowUp': 'up',
            'ArrowDown': 'down',
            'ArrowLeft': 'left',
            'ArrowRight': 'right',
            'w': 'up',
            's': 'down',
            'a': 'left',
            'd': 'right'
        };

        if (keyMap[event.key]) {
            event.preventDefault();
            $wire.keyPressed(keyMap[event.key]);
        }

        // Pause with spacebar
        if (event.key === ' ') {
            event.preventDefault();
            $wire.pauseGame();
        }
    });

    // Listen for game events
    $wire.on('startGameLoop', (speed) => {
        gameSpeed = speed[0];
        startLoop();
    });

    $wire.on('stopGameLoop', () => {
        stopLoop();
    });

    $wire.on('updateGameSpeed', (speed) => {
        gameSpeed = speed[0];
        if (gameLoop) {
            stopLoop();
            startLoop();
        }
    });

    function startLoop() {
        stopLoop();
        gameLoop = setInterval(() => {
            $wire.gameLoop();
        }, gameSpeed);
    }

    function stopLoop() {
        if (gameLoop) {
            clearInterval(gameLoop);
            gameLoop = null;
        }
    }

    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        stopLoop();
    });
</script>
@endscript
</div>
