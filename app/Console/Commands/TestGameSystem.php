<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\User;
use App\Models\GameScore;
use Illuminate\Console\Command;

class TestGameSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the game system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎮 Testing Game System');
        $this->info('=====================');

        // Get a test game
        $game = Game::where('is_active', true)->first();
        if (!$game) {
            $this->error('No active games found. Please create some games first.');
            return 1;
        }

        $this->info("Testing with game: {$game->name} ({$game->icon})");

        // Get a test user
        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please create a user first.');
            return 1;
        }

        $this->info("Testing with user: {$user->name}");

        // Test score submission
        $this->info("\n📊 Testing Score Submission");
        $this->info("---------------------------");

        // Submit some test scores
        $scores = [1000, 1500, 800, 2000, 1200];
        
        foreach ($scores as $score) {
            GameScore::create([
                'game_id' => $game->id,
                'user_id' => $user->id,
                'score' => $score,
                'achieved_at' => now(),
            ]);
            
            $game->updateLeaderboard($user, $score);
            $this->info("Submitted score: {$score}");
        }

        // Test leaderboard
        $this->info("\n🏆 Testing Leaderboard");
        $this->info("----------------------");

        $leaderboard = $game->leaderboard()->with('user')->get();
        foreach ($leaderboard as $entry) {
            $this->info("Rank {$entry->rank}: {$entry->user->name} - {$entry->formatted_score} (played {$entry->total_games_played} times)");
        }

        // Test reset functionality
        $this->info("\n🔄 Testing Reset Functionality");
        $this->info("------------------------------");

        $this->info("Reset frequency: {$game->reset_frequency}");
        if ($game->next_reset_at) {
            $this->info("Next reset: {$game->next_reset_at->format('Y-m-d H:i:s')}");
        }

        $this->info("Needs reset: " . ($game->needsReset() ? 'YES' : 'NO'));

        // Test manual reset
        $this->info("\nTesting manual reset...");
        $entriesBefore = $game->leaderboard()->count();
        $this->info("Leaderboard entries before reset: {$entriesBefore}");

        $game->resetLeaderboard();
        
        $entriesAfter = $game->leaderboard()->count();
        $this->info("Leaderboard entries after reset: {$entriesAfter}");

        // Test game stats
        $this->info("\n📈 Game Statistics");
        $this->info("------------------");

        $totalGames = Game::count();
        $activeGames = Game::active()->count();
        $inactiveGames = $totalGames - $activeGames;

        $this->info("Total games: {$totalGames}");
        $this->info("Active games: {$activeGames}");
        $this->info("Inactive games: {$inactiveGames}");

        // Display all games
        $this->info("\n🎯 All Games");
        $this->info("------------");

        $allGames = Game::ordered()->get();
        foreach ($allGames as $g) {
            $status = $g->is_active ? '✅' : '❌';
            $players = $g->leaderboard()->count();
            $this->info("{$status} {$g->icon} {$g->name} - {$players} players - Resets {$g->reset_frequency}");
        }

        $this->info("\n✅ Game system test completed successfully!");
        
        return 0;
    }
}
