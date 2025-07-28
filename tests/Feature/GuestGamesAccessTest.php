<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestGamesAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_access_games_index()
    {
        $response = $this->get('/games');
        
        $response->assertStatus(200);
        $response->assertSee('Games');
        $response->assertSee('You can play all games as a guest');
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
        $response->assertSee('Snake Game');
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
}
