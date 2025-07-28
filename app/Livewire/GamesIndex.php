<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GamesIndex extends Component
{
    use AuthorizesRequests;

    public function mount()
    {
        // Allow guests to view games, but restrict certain features for authenticated users only
    }

    public function render()
    {
        $games = Game::active()->ordered()->get();

        return view('livewire.games-index', [
            'games' => $games,
        ]);
    }
}
