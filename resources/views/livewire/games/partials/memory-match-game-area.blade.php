{{-- Memory Match Game Area --}}
<div class="text-center">
    <h3 class="text-lg font-semibold mb-4">Memory Match</h3>
    
    <div class="mb-4">
        <p>Game State: <strong>{{ $gameState }}</strong></p>
        <p>Score: <strong>{{ $score }}</strong></p>
    </div>
    
    <div class="flex justify-center space-x-8 mb-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $gameData['moves'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">Moves</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $gameData['matches'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">Matches</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $gameData['timer'] ?? 0 }}s</div>
            <div class="text-sm text-gray-500">Time</div>
        </div>
    </div>
    
    @if($gameState === 'ready')
        <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-12">
            <div class="text-4xl mb-4">🧠</div>
            <p class="text-gray-600">Click cards to flip them and find matching pairs</p>
            <p class="text-gray-600">Complete all matches in the fewest moves possible</p>
            <button wire:click="startGame" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
                Start Game
            </button>
        </div>
    @elseif($gameState === 'playing')
        <div class="inline-block">
            <div class="grid grid-cols-4 gap-2 p-4 bg-gray-50 rounded-lg">
                @if(isset($gameData['cards']) && is_array($gameData['cards']))
                    @foreach($gameData['cards'] as $index => $card)
                        @php
                            $isFlipped = in_array($index, $gameData['flippedCards'] ?? []);
                            $isMatched = in_array($index, $gameData['matchedCards'] ?? []);
                        @endphp
                        <button 
                            wire:click="flipCard({{ $index }})"
                            class="w-16 h-16 rounded-lg border-2 flex items-center justify-center text-2xl font-bold transition-all duration-300
                                   {{ $isMatched ? 'bg-green-200 border-green-400 cursor-not-allowed' : ($isFlipped ? 'bg-blue-200 border-blue-400' : 'bg-gray-200 border-gray-400 hover:bg-gray-300 cursor-pointer') }}"
                            {{ $isFlipped || $isMatched ? 'disabled' : '' }}>
                            {{ $isFlipped || $isMatched ? $card : '❓' }}
                        </button>
                    @endforeach
                @else
                    <p>No cards available</p>
                @endif
            </div>
        </div>
    @elseif($gameState === 'game_over')
        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <h4 class="text-lg font-semibold text-green-800">Congratulations!</h4>
            <p class="text-green-600">Completed in {{ $gameData['moves'] ?? 0 }} moves and {{ $gameData['timer'] ?? 0 }} seconds</p>
            <p class="text-green-600">Final Score: {{ $score }}</p>
            <button wire:click="resetGame" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">
                Play Again
            </button>
        </div>
    @endif
</div>

@if($gameState === 'playing')
<script>
    let memoryTimer;
    
    console.log('Memory match script loaded');
    
    function startMemoryTimer() {
        console.log('Starting memory timer');
        if (memoryTimer) clearInterval(memoryTimer);
        memoryTimer = setInterval(() => {
            console.log('Memory timer tick');
            @this.call('updateTimer').then(() => {
                console.log('Timer updated successfully');
            }).catch(error => {
                console.error('Error updating timer:', error);
            });
        }, 1000);
        console.log('Memory timer interval started');
    }
    
    function stopMemoryTimer() {
        console.log('Stopping memory timer');
        if (memoryTimer) {
            clearInterval(memoryTimer);
            memoryTimer = null;
        }
    }
    
    // Start timer when game starts
    startMemoryTimer();
    
    // Listen for game state changes
    document.addEventListener('livewire:updated', () => {
        const gameState = '{{ $gameState }}';
        console.log('Game state updated to:', gameState);
        if (gameState === 'playing') {
            startMemoryTimer();
        } else {
            stopMemoryTimer();
        }
    });
    
    // Handle mismatched cards
    window.addEventListener('flipCardsBack', () => {
        console.log('Flipping cards back after mismatch');
        setTimeout(() => {
            @this.call('flipCardsBack').then(() => {
                console.log('Cards flipped back successfully');
            }).catch(error => {
                console.error('Error flipping cards back:', error);
            });
        }, 1500);
    });
</script>
@endif
