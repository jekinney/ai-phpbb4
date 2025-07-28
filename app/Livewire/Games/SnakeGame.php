<?php

namespace App\Livewire\Games;

use App\Models\Game;
use App\Models\GameScore;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SnakeGame extends Component
{
    use AuthorizesRequests;

    public $gameId;
    public $game;
    public $score = 0;
    public $gameState = 'ready'; // ready, playing, paused, game_over
    public $boardSize = 20;
    public $snake = [];
    public $food = [];
    public $direction = 'right';
    public $gameSpeed = 200; // milliseconds
    public $startTime;
    public $endTime;
    public $personalBest = 0;
    public $currentRank = null;

    protected $listeners = ['gameLoop', 'keyPressed', 'submitScore'];

    public function mount($gameSlug)
    {
        $this->game = Game::where('slug', $gameSlug)->where('is_active', true)->firstOrFail();
        $this->gameId = $this->game->id;
        
        // Get user's personal best if authenticated
        if (auth()->check()) {
            $leaderboard = auth()->user()->getBestScore($this->game);
            if ($leaderboard) {
                $this->personalBest = $leaderboard->best_score;
                $this->currentRank = $leaderboard->rank;
            }
        }
        
        $this->initializeGame();
    }

    public function initializeGame()
    {
        $this->score = 0;
        $this->direction = 'right';
        $this->gameState = 'ready';
        
        // Initialize snake in the center
        $center = floor($this->boardSize / 2);
        $this->snake = [
            ['x' => $center - 2, 'y' => $center],
            ['x' => $center - 1, 'y' => $center],
            ['x' => $center, 'y' => $center],
        ];
        
        $this->generateFood();
    }

    public function startGame()
    {
        $this->gameState = 'playing';
        $this->startTime = now();
        $this->dispatch('startGameLoop', $this->gameSpeed);
    }

    public function pauseGame()
    {
        if ($this->gameState === 'playing') {
            $this->gameState = 'paused';
            $this->dispatch('stopGameLoop');
        } elseif ($this->gameState === 'paused') {
            $this->gameState = 'playing';
            $this->dispatch('startGameLoop', $this->gameSpeed);
        }
    }

    public function gameLoop()
    {
        if ($this->gameState !== 'playing') {
            return;
        }

        $head = $this->snake[count($this->snake) - 1];
        $newHead = ['x' => $head['x'], 'y' => $head['y']];

        // Move head based on direction
        switch ($this->direction) {
            case 'up':
                $newHead['y']--;
                break;
            case 'down':
                $newHead['y']++;
                break;
            case 'left':
                $newHead['x']--;
                break;
            case 'right':
                $newHead['x']++;
                break;
        }

        // Check wall collision
        if ($newHead['x'] < 0 || $newHead['x'] >= $this->boardSize || 
            $newHead['y'] < 0 || $newHead['y'] >= $this->boardSize) {
            $this->gameOver();
            return;
        }

        // Check self collision
        foreach ($this->snake as $segment) {
            if ($newHead['x'] === $segment['x'] && $newHead['y'] === $segment['y']) {
                $this->gameOver();
                return;
            }
        }

        // Add new head
        $this->snake[] = $newHead;

        // Check food collision
        if ($newHead['x'] === $this->food['x'] && $newHead['y'] === $this->food['y']) {
            $this->score += 10;
            $this->generateFood();
            
            // Increase speed slightly
            $this->gameSpeed = max(50, $this->gameSpeed - 2);
            $this->dispatch('updateGameSpeed', $this->gameSpeed);
        } else {
            // Remove tail if no food eaten
            array_shift($this->snake);
        }
    }

    public function keyPressed($key)
    {
        if ($this->gameState !== 'playing') {
            return;
        }

        $opposites = [
            'up' => 'down',
            'down' => 'up',
            'left' => 'right',
            'right' => 'left',
        ];

        $validDirections = ['up', 'down', 'left', 'right'];

        if (in_array($key, $validDirections) && $key !== $opposites[$this->direction]) {
            $this->direction = $key;
        }
    }

    private function generateFood()
    {
        do {
            $this->food = [
                'x' => rand(0, $this->boardSize - 1),
                'y' => rand(0, $this->boardSize - 1),
            ];
        } while ($this->isFoodOnSnake());
    }

    private function isFoodOnSnake()
    {
        foreach ($this->snake as $segment) {
            if ($this->food['x'] === $segment['x'] && $this->food['y'] === $segment['y']) {
                return true;
            }
        }
        return false;
    }

    public function gameOver()
    {
        $this->gameState = 'game_over';
        $this->endTime = now();
        $this->dispatch('stopGameLoop');
        
        // Auto-submit score if it's a new personal best
        if ($this->score > $this->personalBest) {
            $this->submitScore();
        }
    }

    public function submitScore()
    {
        if (!auth()->check()) {
            session()->flash('info', 'Please login to save your score and compete on the leaderboard!');
            return;
        }

        if ($this->score > 0) {
            // Create game score record
            GameScore::create([
                'game_id' => $this->gameId,
                'user_id' => auth()->id(),
                'score' => $this->score,
                'achieved_at' => $this->endTime ?? now(),
                'metadata' => [
                    'snake_length' => count($this->snake),
                    'game_duration' => $this->startTime ? now()->diffInSeconds($this->startTime) : 0,
                ]
            ]);

            // Update leaderboard
            $this->game->updateLeaderboard(auth()->user(), $this->score);

            // Update personal best and rank
            $leaderboard = auth()->user()->getBestScore($this->game);
            if ($leaderboard) {
                $this->personalBest = $leaderboard->best_score;
                $this->currentRank = $leaderboard->rank;
            }

            session()->flash('success', 'Score submitted successfully!');

            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Score submitted! Your score: ' . $this->score
            ]);
        }
    }

    public function restartGame()
    {
        $this->initializeGame();
    }

    public function render()
    {
        return view('livewire.games.snake-game');
    }
}
