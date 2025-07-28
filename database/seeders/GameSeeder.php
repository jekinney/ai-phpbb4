<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            [
                'name' => 'Snake',
                'description' => 'Classic snake game where you eat food and grow longer while avoiding walls and your own tail.',
                'icon' => '🐍',
                'is_active' => true,
                'scoring_type' => 'highest',
                'max_players_per_game' => 1,
                'reset_frequency' => 'weekly',
                'sort_order' => 1,
            ],
            [
                'name' => 'Tetris',
                'description' => 'Arrange falling blocks to create complete lines and score points.',
                'icon' => '🔳',
                'is_active' => true,
                'scoring_type' => 'highest',
                'max_players_per_game' => 1,
                'reset_frequency' => 'monthly',
                'sort_order' => 2,
            ],
            [
                'name' => '2048',
                'description' => 'Combine numbered tiles to reach the 2048 tile.',
                'icon' => '🔢',
                'is_active' => true,
                'scoring_type' => 'highest',
                'max_players_per_game' => 1,
                'reset_frequency' => 'weekly',
                'sort_order' => 3,
            ],
            [
                'name' => 'Memory Match',
                'description' => 'Match pairs of cards in the fewest moves possible.',
                'icon' => '🧠',
                'is_active' => true,
                'scoring_type' => 'lowest',
                'max_players_per_game' => 1,
                'reset_frequency' => 'daily',
                'sort_order' => 4,
            ],
            [
                'name' => 'Speed Typing',
                'description' => 'Type as fast and accurately as possible.',
                'icon' => '⌨️',
                'is_active' => true,
                'scoring_type' => 'time',
                'max_players_per_game' => 1,
                'reset_frequency' => 'daily',
                'sort_order' => 5,
            ],
            [
                'name' => 'Puzzle Rush',
                'description' => 'Solve puzzles as quickly as possible.',
                'icon' => '🧩',
                'is_active' => false,
                'scoring_type' => 'time',
                'max_players_per_game' => 1,
                'reset_frequency' => 'never',
                'sort_order' => 6,
            ],
        ];

        foreach ($games as $gameData) {
            // Extract the name to use as the unique identifier
            $name = $gameData['name'];
            
            // Use updateOrCreate to avoid duplicate key errors
            $game = Game::updateOrCreate(
                ['name' => $name], // Search criteria
                $gameData // Data to create or update
            );
            
            // Set next reset date if frequency is not 'never'
            if ($game->reset_frequency !== 'never') {
                $game->update([
                    'next_reset_at' => $game->calculateNextReset()
                ]);
            }
        }
    }
}
