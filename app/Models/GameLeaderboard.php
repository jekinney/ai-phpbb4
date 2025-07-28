<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameLeaderboard extends Model
{
    protected $fillable = [
        'game_id',
        'user_id',
        'rank',
        'best_score',
        'best_time',
        'total_games_played',
        'last_played_at',
    ];

    protected $casts = [
        'last_played_at' => 'datetime',
    ];

    /**
     * Get the game this leaderboard entry belongs to.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the user this leaderboard entry belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for top players (top 10).
     */
    public function scopeTopPlayers($query)
    {
        return $query->orderBy('rank')->limit(10);
    }

    /**
     * Get the formatted score display.
     */
    public function getFormattedScoreAttribute(): string
    {
        return number_format($this->best_score, 0);
    }

    /**
     * Get the formatted time display.
     */
    public function getFormattedTimeAttribute(): ?string
    {
        if (!$this->best_time) {
            return null;
        }

        $minutes = floor($this->best_time / 60);
        $seconds = $this->best_time % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
