<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameScore extends Model
{
    protected $fillable = [
        'game_id',
        'user_id',
        'score',
        'time_taken',
        'metadata',
        'achieved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'achieved_at' => 'datetime',
    ];

    /**
     * Get the game this score belongs to.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the user who achieved this score.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for recent scores.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('achieved_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for best scores by game.
     */
    public function scopeBestForGame($query, $gameId)
    {
        return $query->where('game_id', $gameId)
                    ->orderByDesc('score')
                    ->limit(10);
    }
}
