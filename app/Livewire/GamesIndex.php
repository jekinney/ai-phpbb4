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
        $this->authorize('view_leaderboards');
    }

    public function render()
    {
        $games = Game::active()->ordered()->get();

        return view('livewire.games-index', [
            'games' => $games,
        ]);
    }
}
