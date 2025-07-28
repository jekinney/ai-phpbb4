<?php
// Quick debug script to test game access
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Game;

echo "=== Games in Database ===\n";
$games = Game::all();
foreach ($games as $game) {
    echo "ID: {$game->id}, Name: {$game->name}, Slug: {$game->slug}, Active: " . ($game->is_active ? 'Yes' : 'No') . "\n";
}

echo "\n=== Testing Snake Game Access ===\n";
try {
    $snakeGame = Game::where('slug', 'snake')->first();
    if ($snakeGame) {
        echo "Snake game found: {$snakeGame->name}\n";
        echo "URL should be: /games/{$snakeGame->slug}\n";
    } else {
        echo "Snake game NOT found in database!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Memory Match Game Access ===\n";
try {
    $memoryGame = Game::where('slug', 'memory-match')->first();
    if ($memoryGame) {
        echo "Memory game found: {$memoryGame->name}\n";
        echo "URL should be: /games/{$memoryGame->slug}\n";
    } else {
        echo "Memory game NOT found in database!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
