<?php

namespace App\Jobs;

use App\Models\Game;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ResetGameLeaderboards implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Running automatic game leaderboard reset job');

        $gamesNeedingReset = Game::where('reset_frequency', '!=', 'never')
                                ->where('next_reset_at', '<=', now())
                                ->get();

        foreach ($gamesNeedingReset as $game) {
            try {
                $game->resetLeaderboard();
                Log::info("Reset leaderboard for game: {$game->name} (ID: {$game->id})");
            } catch (\Exception $e) {
                Log::error("Failed to reset leaderboard for game {$game->name} (ID: {$game->id}): " . $e->getMessage());
            }
        }

        Log::info("Completed automatic game leaderboard reset job. Reset {$gamesNeedingReset->count()} games.");
    }
}
