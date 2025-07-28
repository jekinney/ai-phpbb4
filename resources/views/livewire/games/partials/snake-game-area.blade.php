{{-- Snake Game Area --}}
<div class="text-center">
    <h3 class="text-lg font-semibold mb-4">Snake Game</h3>
    
    <div class="mb-4">
        <p>Game State: <strong>{{ $gameState }}</strong></p>
        <p>Score: <strong>{{ $score }}</strong></p>
        @if(isset($gameData['direction']))
            <p>Direction: <strong>{{ $gameData['direction'] }}</strong></p>
        @endif
        @if(isset($gameData['snake']))
            <p>Snake Length: <strong>{{ count($gameData['snake']) }}</strong></p>
        @endif
    </div>
    
    @if($gameState === 'ready')
        <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-12">
            <div class="text-4xl mb-4">🐍</div>
            <p class="text-gray-600">Use arrow keys or WASD to control the snake</p>
            <p class="text-gray-600">Eat food to grow and increase your score</p>
            <button wire:click="startGame" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
                Start Game
            </button>
        </div>
    @elseif($gameState === 'playing')
        <div class="bg-black rounded-lg mx-auto mb-4" style="width: 400px; height: 400px; position: relative;">
            <div id="snake-canvas" class="w-full h-full relative overflow-hidden">
                {{-- Snake segments --}}
                @if(isset($gameData['snake']) && is_array($gameData['snake']))
                    @foreach($gameData['snake'] as $index => $segment)
                        <div class="absolute {{ $index === count($gameData['snake']) - 1 ? 'bg-green-400' : 'bg-green-500' }}"
                             style="width: 20px; height: 20px; left: {{ $segment[0] * 20 }}px; top: {{ $segment[1] * 20 }}px;">
                        </div>
                    @endforeach
                @endif
                
                {{-- Food --}}
                @if(isset($gameData['food']) && is_array($gameData['food']))
                    <div class="absolute bg-red-500 rounded-full"
                         style="width: 20px; height: 20px; left: {{ $gameData['food'][0] * 20 }}px; top: {{ $gameData['food'][1] * 20 }}px;">
                    </div>
                @endif
            </div>
        </div>
        
        <div class="space-x-2">
            <button wire:click="moveSnake('up')" class="bg-blue-500 text-white px-3 py-1 rounded">↑</button>
            <button wire:click="moveSnake('down')" class="bg-blue-500 text-white px-3 py-1 rounded">↓</button>
            <button wire:click="moveSnake('left')" class="bg-blue-500 text-white px-3 py-1 rounded">←</button>
            <button wire:click="moveSnake('right')" class="bg-blue-500 text-white px-3 py-1 rounded">→</button>
        </div>
        
    @elseif($gameState === 'game_over')
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h4 class="text-lg font-semibold text-red-800">Game Over!</h4>
            <p class="text-red-600">Final Score: {{ $score }}</p>
            <p class="text-red-600">Snake Length: {{ count($gameData['snake'] ?? []) }}</p>
            <button wire:click="resetGame" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">
                Play Again
            </button>
        </div>
    @endif
</div>

@if($gameState === 'playing')
    <script>
        let gameInterval = null;
        
        console.log('Snake game script loaded');
        
        function startSnakeGame() {
            console.log('Starting snake game...');
            if (gameInterval) {
                clearInterval(gameInterval);
                console.log('Cleared existing interval');
            }
            
            // Start auto-movement every 200ms
            gameInterval = setInterval(() => {
                console.log('Auto-moving snake...');
                @this.call('processSnakeMove').then(() => {
                    console.log('Snake move processed');
                }).catch(error => {
                    console.error('Error processing snake move:', error);
                });
            }, 200);
            console.log('Game interval started');
        }
        
        // Arrow key controls
        document.addEventListener('keydown', function(e) {
            let direction = null;
            switch(e.key) {
                case 'ArrowUp': direction = 'up'; break;
                case 'ArrowDown': direction = 'down'; break;
                case 'ArrowLeft': direction = 'left'; break;
                case 'ArrowRight': direction = 'right'; break;
            }
            if (direction) {
                e.preventDefault();
                console.log('Setting direction to:', direction);
                @this.set('currentDirection', direction);
            }
        });
        
        // Manual direction buttons
        window.setSnakeDirection = function(direction) {
            console.log('Manual direction set to:', direction);
            @this.set('currentDirection', direction);
        }
        
        // Auto-start when component loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, starting snake game');
            startSnakeGame();
        });
        
        // Also start when Livewire loads
        document.addEventListener('livewire:init', function() {
            console.log('Livewire initialized, starting snake game');
            startSnakeGame();
        });
    </script>
@endif
