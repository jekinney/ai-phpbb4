<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestGamesAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_access_games_index()
    {
        // Create test games - both playable and non-playable
        \App\Models\Game::create([
            'name' => 'Snake',
            'slug' => 'snake',
            'description' => 'Classic snake game',
            'is_active' => true,
            'icon' => '🐍',
            'scoring_type' => 'points',
            'reset_frequency' => 'never',
            'sort_order' => 1,
        ]);

        \App\Models\Game::create([
            'name' => 'Memory Match',
            'slug' => 'memory-match',
            'description' => 'Match pairs of cards',
            'is_active' => true,
            'icon' => '🧠',
            'scoring_type' => 'points',
            'reset_frequency' => 'never',
            'sort_order' => 2,
        ]);

        \App\Models\Game::create([
            'name' => 'Tetris',
            'slug' => 'tetris',
            'description' => 'Arrange falling blocks',
            'is_active' => true,
            'icon' => '🔳',
            'scoring_type' => 'points',
            'reset_frequency' => 'never',
            'sort_order' => 3,
        ]);

        $response = $this->get('/games');

        $response->assertStatus(200);
        $response->assertSee('Games');
        $response->assertSee('You can play all games as a guest');
        
        // Test that ALL game names are clickable links (both playable and non-playable)
        $response->assertSee('Snake');
        $response->assertSee('Memory Match');
        $response->assertSee('Tetris');
        
        // All game names should have href attributes (making them clickable)
        $response->assertSee('href=', false); // At least some href attributes should be present
        
        // All games should now link to the universal route
        $response->assertSee('href="' . route('games.show', 'snake') . '"', false);
        $response->assertSee('href="' . route('games.show', 'memory-match') . '"', false);
        $response->assertSee('href="' . route('games.show', 'tetris') . '"', false);
    }

    public function test_guests_can_access_snake_game()
    {
        // Create a test game first
        \App\Models\Game::create([
            'name' => 'Snake',
            'slug' => 'snake',
            'description' => 'Classic snake game',
            'is_active' => true,
            'icon' => '🐍',
            'scoring_type' => 'points',
            'reset_frequency' => 'never',
        ]);

        $response = $this->get('/games/snake');
        
        $response->assertStatus(200);
        $response->assertSee('Snake');
    }

    public function test_guests_can_access_memory_match_game()
    {
        // Create a test game first
        \App\Models\Game::create([
            'name' => 'Memory Match',
            'slug' => 'memory-match',
            'description' => 'Match pairs of cards',
            'is_active' => true,
            'icon' => '🧠',
            'scoring_type' => 'points',
            'reset_frequency' => 'never',
        ]);

        $response = $this->get('/games/memory-match');
        
        $response->assertStatus(200);
        $response->assertSee('Memory Match');
    }

    public function test_guests_can_access_tetris_game()
    {
        // Create a test game first
        \App\Models\Game::create([
            'name' => 'Tetris',
            'slug' => 'tetris',
            'description' => 'Arrange falling blocks',
            'is_active' => true,
            'icon' => '🔳',
            'scoring_type' => 'points',
            'reset_frequency' => 'never',
        ]);

        $response = $this->get('/games/tetris');
        
        $response->assertStatus(200);
        $response->assertSee('Tetris');
        $response->assertSee('This game is coming soon');
    }
}
