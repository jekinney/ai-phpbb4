<?php

namespace App\Livewire\Games;

use App\Models\Game;
use App\Models\GameScore;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class GamePage extends Component
{
    use AuthorizesRequests;

    public Game $game;
    public $score = 0;
    public $gameState = 'ready'; // ready, playing, paused, game_over
    public $personalBest = 0;
    public $currentRank = null;
    public $gameData = []; // For storing game-specific data

    protected $listeners = [
        'processSnakeMove',
        'moveSnake', 
        'flipCard',
        'flipCardsBack',
        'updateTimer'
    ];

    public function mount(Game $game)
    {
        $this->game = $game;
        
        // Get user's personal best if authenticated
        if (Auth::check()) {
            $bestScore = GameScore::where('game_id', $this->game->id)
                ->where('user_id', Auth::id())
                ->orderBy('score', $this->game->scoring_type === 'highest' ? 'desc' : 'asc')
                ->first();
            
            $this->personalBest = $bestScore ? $bestScore->score : 0;
            
            // Get current rank
            $this->currentRank = $this->getCurrentRank();
        }

        // Initialize game-specific data
        $this->initializeGameData();
    }

    public function initializeGameData()
    {
        // Initialize different data structures based on game type
        switch ($this->game->slug) {
            case 'snake':
                $boardSize = 20;
                $center = floor($boardSize / 2);
                $this->gameData = [
                    'boardSize' => $boardSize,
                    'snake' => [
                        [$center - 2, $center],
                        [$center - 1, $center],
                        [$center, $center]
                    ],
                    'food' => [$center + 5, $center],
                    'direction' => 'right',
                    'gameSpeed' => 200
                ];
                $this->generateSnakeFood();
                break;
                
            case 'memory-match':
                $this->gameData = [
                    'gridSize' => 4,
                    'cards' => [],
                    'flippedCards' => [],
                    'matchedCards' => [],
                    'moves' => 0,
                    'matches' => 0,
                    'timer' => 0,
                ];
                $this->generateMemoryCards();
                break;
                
            case 'tetris':
                $this->gameData = [
                    'board' => array_fill(0, 20, array_fill(0, 10, 0)),
                    'currentPiece' => null,
                    'level' => 1,
                    'lines' => 0
                ];
                break;
                
            case '2048':
                $this->gameData = [
                    'board' => array_fill(0, 4, array_fill(0, 4, 0)),
                    'moves' => 0
                ];
                $this->spawn2048Tile();
                $this->spawn2048Tile();
                break;
                
            default:
                $this->gameData = [];
        }
    }

    private function generateMemoryCards()
    {
        $symbols = ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼'];
        $gridSize = $this->gameData['gridSize'];
        $totalCards = $gridSize * $gridSize;
        $pairs = $totalCards / 2;
        
        $cards = [];
        for ($i = 0; $i < $pairs; $i++) {
            $cards[] = $symbols[$i % count($symbols)];
            $cards[] = $symbols[$i % count($symbols)];
        }
        
        shuffle($cards);
        $this->gameData['cards'] = $cards;
    }

    private function spawn2048Tile()
    {
        $emptyCells = [];
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                if ($this->gameData['board'][$i][$j] === 0) {
                    $emptyCells[] = [$i, $j];
                }
            }
        }
        
        if (!empty($emptyCells)) {
            $randomCell = $emptyCells[array_rand($emptyCells)];
            $this->gameData['board'][$randomCell[0]][$randomCell[1]] = rand(1, 10) <= 9 ? 2 : 4;
        }
    }

    public function startGame()
    {
        $this->gameState = 'playing';
        $this->score = 0;
        $this->initializeGameData();
        
        // Dispatch game-specific start events
        switch ($this->game->slug) {
            case 'snake':
                $this->dispatch('startSnakeGame');
                break;
            case 'memory-match':
                $this->startMemoryMatchTimer();
                break;
        }
    }

    public function pauseGame()
    {
        $this->gameState = $this->gameState === 'paused' ? 'playing' : 'paused';
        
        if ($this->game->slug === 'snake') {
            $this->dispatch($this->gameState === 'paused' ? 'pauseSnakeGame' : 'resumeSnakeGame');
        }
    }

    public function resetGame()
    {
        $this->gameState = 'ready';
        $this->score = 0;
        $this->initializeGameData();
        
        if ($this->game->slug === 'snake') {
            $this->dispatch('stopSnakeGame');
        }
    }

    // Snake-specific methods
    public function moveSnake($direction)
    {
        if ($this->gameState !== 'playing' || $this->game->slug !== 'snake') {
            return;
        }
        
        // Prevent reversing direction
        $currentDirection = $this->gameData['direction'];
        $oppositeDirections = [
            'up' => 'down',
            'down' => 'up',
            'left' => 'right',
            'right' => 'left'
        ];
        
        if ($oppositeDirections[$currentDirection] === $direction) {
            return; // Can't reverse direction
        }
        
        $this->gameData['direction'] = $direction;
    }

    public function processSnakeMove()
    {
        if ($this->gameState !== 'playing' || $this->game->slug !== 'snake') {
            return;
        }
        
        $snake = $this->gameData['snake'];
        $head = end($snake);
        $newHead = $head;
        
        // Move based on direction
        switch ($this->gameData['direction']) {
            case 'up':
                $newHead[1]--;
                break;
            case 'down':
                $newHead[1]++;
                break;
            case 'left':
                $newHead[0]--;
                break;
            case 'right':
                $newHead[0]++;
                break;
        }
        
        // Check walls
        $boardSize = $this->gameData['boardSize'];
        if ($newHead[0] < 0 || $newHead[0] >= $boardSize || 
            $newHead[1] < 0 || $newHead[1] >= $boardSize) {
            $this->gameOver();
            return;
        }
        
        // Check self collision
        foreach ($snake as $segment) {
            if ($segment[0] === $newHead[0] && $segment[1] === $newHead[1]) {
                $this->gameOver();
                return;
            }
        }
        
        // Add new head
        $snake[] = $newHead;
        
        // Check food collision
        $food = $this->gameData['food'];
        if ($newHead[0] === $food[0] && $newHead[1] === $food[1]) {
            $this->score += 10;
            $this->generateSnakeFood();
        } else {
            // Remove tail if no food eaten
            array_shift($snake);
        }
        
        $this->gameData['snake'] = $snake;
    }

    private function generateSnakeFood()
    {
        $boardSize = $this->gameData['boardSize'];
        $snake = $this->gameData['snake'];
        
        do {
            $food = [rand(0, $boardSize - 1), rand(0, $boardSize - 1)];
            $onSnake = false;
            
            foreach ($snake as $segment) {
                if ($segment[0] === $food[0] && $segment[1] === $food[1]) {
                    $onSnake = true;
                    break;
                }
            }
        } while ($onSnake);
        
        $this->gameData['food'] = $food;
    }

    // Memory Match specific methods
    public function flipCard($index)
    {
        if ($this->gameState !== 'playing' || $this->game->slug !== 'memory-match') {
            return;
        }
        
        $flippedCards = $this->gameData['flippedCards'];
        $matchedCards = $this->gameData['matchedCards'];
        
        // Don't flip if already flipped or matched
        if (in_array($index, $flippedCards) || in_array($index, $matchedCards)) {
            return;
        }
        
        // Don't flip if already have 2 cards flipped
        if (count($flippedCards) >= 2) {
            return;
        }
        
        $flippedCards[] = $index;
        $this->gameData['flippedCards'] = $flippedCards;
        $this->gameData['moves']++;
        
        // Check for match if 2 cards are flipped
        if (count($flippedCards) === 2) {
            $card1 = $this->gameData['cards'][$flippedCards[0]];
            $card2 = $this->gameData['cards'][$flippedCards[1]];
            
            if ($card1 === $card2) {
                // Match found
                $this->gameData['matchedCards'] = array_merge($matchedCards, $flippedCards);
                $this->gameData['flippedCards'] = [];
                $this->gameData['matches']++;
                $this->score += 100;
                
                // Check if game is complete
                if (count($this->gameData['matchedCards']) === count($this->gameData['cards'])) {
                    $this->gameState = 'game_over';
                    $this->score += max(0, 1000 - ($this->gameData['moves'] * 10)); // Bonus for fewer moves
                }
            } else {
                // No match - flip back after delay
                $this->dispatch('flipCardsBack');
            }
        }
    }

    public function flipCardsBack()
    {
        $this->gameData['flippedCards'] = [];
    }

    private function startMemoryMatchTimer()
    {
        $this->gameData['timer'] = 0;
        $this->dispatch('startMemoryTimer');
    }

    public function updateTimer()
    {
        if ($this->gameState === 'playing' && $this->game->slug === 'memory-match') {
            $this->gameData['timer']++;
        }
    }

    private function gameOver()
    {
        $this->gameState = 'game_over';
        $this->endTime = now();
        
        if ($this->game->slug === 'snake') {
            $this->dispatch('stopSnakeGame');
        }
    }

    public function submitScore()
    {
        if (!Auth::check()) {
            return; // Guests can't submit scores
        }

        GameScore::create([
            'game_id' => $this->game->id,
            'user_id' => Auth::id(),
            'score' => $this->score,
            'moves' => $this->gameData['moves'] ?? null,
            'time_taken' => $this->gameData['timer'] ?? null,
            'game_data' => json_encode($this->gameData),
        ]);

        $this->personalBest = $this->getPersonalBest();
        $this->currentRank = $this->getCurrentRank();
    }

    private function getPersonalBest()
    {
        if (!Auth::check()) return 0;
        
        $bestScore = GameScore::where('game_id', $this->game->id)
            ->where('user_id', Auth::id())
            ->orderBy('score', $this->game->scoring_type === 'highest' ? 'desc' : 'asc')
            ->first();
            
        return $bestScore ? $bestScore->score : 0;
    }

    private function getCurrentRank()
    {
        if (!Auth::check()) return null;
        
        $userBestScore = $this->getPersonalBest();
        if (!$userBestScore) return null;

        if ($this->game->scoring_type === 'highest') {
            return GameScore::where('game_id', $this->game->id)
                ->where('score', '>', $userBestScore)
                ->distinct('user_id')
                ->count() + 1;
        } else {
            return GameScore::where('game_id', $this->game->id)
                ->where('score', '<', $userBestScore)
                ->distinct('user_id')
                ->count() + 1;
        }
    }

    public function updateGameData($data)
    {
        $this->gameData = array_merge($this->gameData, $data);
    }

    public function render()
    {
        // Always use the generic view for the universal game page
        return view('livewire.games.game-page', [
            'topScores' => $this->getTopScores(),
        ]);
    }

    private function getTopScores()
    {
        return GameScore::where('game_id', $this->game->id)
            ->with('user')
            ->orderBy('score', $this->game->scoring_type === 'highest' ? 'desc' : 'asc')
            ->limit(10)
            ->get();
    }
}
