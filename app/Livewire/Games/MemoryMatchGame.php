<?php

namespace App\Livewire\Games;

use App\Models\Game;
use App\Models\GameScore;
use App\Models\GameLeaderboard;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MemoryMatchGame extends Component
{
    public $gameState = 'ready'; // ready, playing, game_over
    public $score = 0;
    public $moves = 0;
    public $matches = 0;
    public $timer = 0;
    public $cards = [];
    public $flippedCards = [];
    public $matchedCards = [];
    public $personalBest = 0;
    public $currentRank = null;
    public $gridSize = 4; // 4x4 grid = 16 cards (8 pairs)
    protected $game;

    // Card symbols for the game
    protected $cardSymbols = [
        '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼',
        '🐨', '🐯', '🦁', '🐸', '🐵', '🐔', '🐧', '🦆',
        '🦋', '🐌', '🐛', '🐜', '🐝', '🪲', '🦗', '🕷️'
    ];

    public function mount($gameSlug = 'memory-match')
    {
        $this->game = Game::where('slug', $gameSlug)->first();
        if (!$this->game || !$this->game->is_active) {
            abort(404, 'Game not found or is not active');
        }

        $this->loadPersonalBest();
        $this->initializeCards();
    }

    protected function loadPersonalBest()
    {
        if (!Auth::check()) {
            return;
        }

        $bestScore = GameScore::where('game_id', $this->game->id)
            ->where('user_id', Auth::id())
            ->max('score');

        $this->personalBest = $bestScore ?? 0;

        // Get current rank
        $leaderboard = GameLeaderboard::where('game_id', $this->game->id)
            ->where('user_id', Auth::id())
            ->first();

        $this->currentRank = $leaderboard?->rank;
    }

    protected function initializeCards()
    {
        // Select random symbols for the game
        $selectedSymbols = collect($this->cardSymbols)
            ->random(($this->gridSize * $this->gridSize) / 2)
            ->values()
            ->toArray();

        // Create pairs and shuffle
        $cardData = array_merge($selectedSymbols, $selectedSymbols);
        shuffle($cardData);

        // Initialize card structure
        $this->cards = [];
        for ($i = 0; $i < count($cardData); $i++) {
            $this->cards[] = [
                'id' => $i,
                'symbol' => $cardData[$i],
                'flipped' => false,
                'matched' => false
            ];
        }
    }

    public function startGame()
    {
        $this->gameState = 'playing';
        $this->score = 0;
        $this->moves = 0;
        $this->matches = 0;
        $this->timer = 0;
        $this->flippedCards = [];
        $this->matchedCards = [];
        $this->initializeCards();
        
        $this->dispatch('startTimer');
    }

    public function restartGame()
    {
        $this->startGame();
    }

    public function flipCard($cardId)
    {
        if ($this->gameState !== 'playing') {
            return;
        }

        $card = &$this->cards[$cardId];

        // Can't flip if already flipped, matched, or we have 2 cards flipped
        if ($card['flipped'] || $card['matched'] || count($this->flippedCards) >= 2) {
            return;
        }

        // Flip the card
        $card['flipped'] = true;
        $this->flippedCards[] = $cardId;

        // If we have 2 cards flipped, check for match
        if (count($this->flippedCards) === 2) {
            $this->moves++;
            $this->checkForMatch();
        }
    }

    protected function checkForMatch()
    {
        $card1Id = $this->flippedCards[0];
        $card2Id = $this->flippedCards[1];
        
        $card1 = $this->cards[$card1Id];
        $card2 = $this->cards[$card2Id];

        if ($card1['symbol'] === $card2['symbol']) {
            // Match found!
            $this->cards[$card1Id]['matched'] = true;
            $this->cards[$card2Id]['matched'] = true;
            $this->matchedCards[] = $card1Id;
            $this->matchedCards[] = $card2Id;
            $this->matches++;
            
            // Calculate score (bonus for fewer moves)
            $this->score += 100 + max(0, 50 - $this->moves);
            
            $this->flippedCards = [];
            
            // Check if game is complete
            if ($this->matches === ($this->gridSize * $this->gridSize) / 2) {
                $this->completeGame();
            }
        } else {
            // No match - flip cards back after delay
            $this->dispatch('flipCardsBack', [
                'cardIds' => $this->flippedCards,
                'delay' => 1000
            ]);
        }
    }

    public function flipCardsBack($cardIds)
    {
        foreach ($cardIds as $cardId) {
            $this->cards[$cardId]['flipped'] = false;
        }
        $this->flippedCards = [];
    }

    protected function completeGame()
    {
        $this->gameState = 'game_over';
        
        // Calculate final score with time bonus
        $timeBonus = max(0, 300 - $this->timer); // Bonus for completing quickly
        $this->score += $timeBonus;
        
        $this->dispatch('stopTimer');
        
        // Auto-submit score if it's a personal best
        if (Auth::check() && $this->score > $this->personalBest) {
            $this->submitScore();
        }
    }

    public function submitScore()
    {
        if (!Auth::check()) {
            session()->flash('info', 'Please login to save your score and compete on the leaderboard!');
            return;
        }
        
        if ($this->score <= 0) {
            return;
        }

        try {
            // Save the score
            GameScore::create([
                'game_id' => $this->game->id,
                'user_id' => Auth::id(),
                'score' => $this->score,
                'metadata' => [
                    'moves' => $this->moves,
                    'time' => $this->timer,
                    'matches' => $this->matches
                ]
            ]);

            // Update leaderboard
            $this->updateLeaderboard();
            
            // Refresh personal best
            $this->loadPersonalBest();

            session()->flash('success', 'Score submitted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit score. Please try again.');
        }
    }

    protected function updateLeaderboard()
    {
        $topScores = GameScore::where('game_id', $this->game->id)
            ->selectRaw('user_id, MAX(score) as best_score')
            ->groupBy('user_id')
            ->orderByDesc('best_score')
            ->limit(100)
            ->get();

        // Clear existing leaderboard
        GameLeaderboard::where('game_id', $this->game->id)->delete();

        // Insert new rankings
        foreach ($topScores as $index => $scoreData) {
            GameLeaderboard::create([
                'game_id' => $this->game->id,
                'user_id' => $scoreData->user_id,
                'score' => $scoreData->best_score,
                'rank' => $index + 1
            ]);
        }
    }

    public function updateTimer($time)
    {
        $this->timer = $time;
    }

    public function render()
    {
        return view('livewire.games.memory-match-game');
    }
}
