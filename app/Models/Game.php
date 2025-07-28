<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Game extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'scoring_type',
        'max_players_per_game',
        'settings',
        'reset_frequency',
        'last_reset_at',
        'next_reset_at',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'last_reset_at' => 'datetime',
        'next_reset_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            if (empty($game->slug)) {
                $game->slug = Str::slug($game->name);
            }
        });

        static::updating(function ($game) {
            if ($game->isDirty('name') && empty($game->slug)) {
                $game->slug = Str::slug($game->name);
            }
        });
    }

    /**
     * Get all scores for this game.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(GameScore::class);
    }

    /**
     * Get the leaderboard for this game.
     */
    public function leaderboard(): HasMany
    {
        return $this->hasMany(GameLeaderboard::class)->orderBy('rank');
    }

    /**
     * Get top 10 players for this game.
     */
    public function topPlayers()
    {
        return $this->leaderboard()->with('user')->limit(10)->get();
    }

    /**
     * Check if the game needs to be reset.
     */
    public function needsReset(): bool
    {
        if ($this->reset_frequency === 'never' || !$this->next_reset_at) {
            return false;
        }

        return now()->gte($this->next_reset_at);
    }

    /**
     * Calculate the next reset time based on frequency.
     */
    public function calculateNextReset(): ?Carbon
    {
        if ($this->reset_frequency === 'never') {
            return null;
        }

        $now = now();
        
        return match($this->reset_frequency) {
            'daily' => $now->addDay()->startOfDay(),
            'weekly' => $now->addWeek()->startOfWeek(),
            'monthly' => $now->addMonth()->startOfMonth(),
            default => null,
        };
    }

    /**
     * Reset the leaderboard for this game.
     */
    public function resetLeaderboard(): void
    {
        // Clear existing leaderboard
        $this->leaderboard()->delete();
        
        // Update reset timestamps
        $this->update([
            'last_reset_at' => now(),
            'next_reset_at' => $this->calculateNextReset(),
        ]);
    }

    /**
     * Update leaderboard when a new score is submitted.
     */
    public function updateLeaderboard(User $user, float $score, ?int $timeTaken = null): void
    {
        $leaderboardEntry = $this->leaderboard()->where('user_id', $user->id)->first();

        if ($leaderboardEntry) {
            // Check if this is a better score
            $isBetter = match($this->scoring_type) {
                'highest' => $score > $leaderboardEntry->best_score,
                'lowest' => $score < $leaderboardEntry->best_score,
                'time' => $timeTaken && (!$leaderboardEntry->best_time || $timeTaken < $leaderboardEntry->best_time),
                default => false,
            };

            if ($isBetter) {
                $leaderboardEntry->update([
                    'best_score' => $score,
                    'best_time' => $timeTaken,
                    'total_games_played' => $leaderboardEntry->total_games_played + 1,
                    'last_played_at' => now(),
                ]);
            } else {
                $leaderboardEntry->increment('total_games_played');
                $leaderboardEntry->update(['last_played_at' => now()]);
            }
        } else {
            // Create new leaderboard entry
            $this->leaderboard()->create([
                'user_id' => $user->id,
                'rank' => 1, // Will be recalculated
                'best_score' => $score,
                'best_time' => $timeTaken,
                'total_games_played' => 1,
                'last_played_at' => now(),
            ]);
        }

        // Recalculate ranks
        $this->recalculateRanks();
    }

    /**
     * Recalculate ranks for the leaderboard.
     */
    public function recalculateRanks(): void
    {
        $orderBy = match($this->scoring_type) {
            'highest' => 'best_score DESC',
            'lowest' => 'best_score ASC',
            'time' => 'best_time ASC',
            default => 'best_score DESC',
        };

        $leaderboard = $this->leaderboard()->orderByRaw($orderBy)->get();
        
        foreach ($leaderboard as $index => $entry) {
            $entry->update(['rank' => $index + 1]);
        }
    }

    /**
     * Scope for active games only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for games ordered by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the URL for this game.
     */
    public function getUrlAttribute()
    {
        return route('games.show', $this);
    }

    /**
     * Check if this game has a playable route.
     */
    public function hasPlayableRoute()
    {
        // All games use the universal route now
        return true;
    }
}
