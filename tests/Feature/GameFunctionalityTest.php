<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Game;

class GameFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    public function test_snake_game_functionality()
    {
        // Create a snake game
        $game = Game::create([
            'name' => 'Snake',
            'slug' => 'snake',
            'description' => 'Classic snake game',
            'is_active' => true,
            'icon' => '🐍',
            'scoring_type' => 'highest',
            'reset_frequency' => 'never',
        ]);

        // Test the component loads
        $component = Livewire::test(\App\Livewire\Games\GamePage::class, ['game' => $game]);
        
        $component->assertSee('Snake Game')
                  ->assertSee('Start Game');

        // Test starting the game
        $component->call('startGame');
        $component->assertSet('gameState', 'playing');
        
        // Test moving the snake
        $component->call('moveSnake', 'up');
        $this->assertEquals('up', $component->get('gameData.direction'));
        
        // Test resetting the game
        $component->call('resetGame');
        $component->assertSet('gameState', 'ready');
    }

    public function test_memory_match_game_functionality()
    {
        // Create a memory match game
        $game = Game::create([
            'name' => 'Memory Match',
            'slug' => 'memory-match',
            'description' => 'Match pairs of cards',
            'is_active' => true,
            'icon' => '🧠',
            'scoring_type' => 'lowest',
            'reset_frequency' => 'never',
        ]);

        $component = Livewire::test(\App\Livewire\Games\GamePage::class, ['game' => $game]);
        
        $component->assertSee('Memory Match')
                  ->assertSee('Start Game');

        // Test starting the game
        $component->call('startGame');
        $component->assertSet('gameState', 'playing');
        
        // Check that cards are generated
        $this->assertIsArray($component->get('gameData.cards'));
        $this->assertCount(16, $component->get('gameData.cards')); // 4x4 grid
        
        // Test flipping a card
        $component->call('flipCard', 0);
        $this->assertContains(0, $component->get('gameData.flippedCards'));
    }
}
