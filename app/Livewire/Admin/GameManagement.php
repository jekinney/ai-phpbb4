<?php

namespace App\Livewire\Admin;

use App\Models\Game;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GameManagement extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedGame = null;

    // Form fields
    public $name = '';
    public $description = '';
    public $icon = '';
    public $is_active = false;
    public $scoring_type = 'highest';
    public $max_players_per_game = 1;
    public $reset_frequency = 'never';
    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'icon' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'scoring_type' => 'required|in:highest,lowest,time',
        'max_players_per_game' => 'required|integer|min:1|max:100',
        'reset_frequency' => 'required|in:never,daily,weekly,monthly',
        'sort_order' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->authorize('manage_games');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($gameId)
    {
        $game = Game::findOrFail($gameId);
        $this->selectedGame = $game;
        
        $this->name = $game->name;
        $this->description = $game->description;
        $this->icon = $game->icon;
        $this->is_active = $game->is_active;
        $this->scoring_type = $game->scoring_type;
        $this->max_players_per_game = $game->max_players_per_game;
        $this->reset_frequency = $game->reset_frequency;
        $this->sort_order = $game->sort_order;
        
        $this->showEditModal = true;
    }

    public function openDeleteModal($gameId)
    {
        $this->selectedGame = Game::findOrFail($gameId);
        $this->showDeleteModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->selectedGame = null;
        $this->resetForm();
    }

    public function createGame()
    {
        $this->validate();

        Game::create([
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'scoring_type' => $this->scoring_type,
            'max_players_per_game' => $this->max_players_per_game,
            'reset_frequency' => $this->reset_frequency,
            'sort_order' => $this->sort_order,
            'next_reset_at' => $this->reset_frequency !== 'never' ? now()->addDay() : null,
        ]);

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'Game created successfully!'
        ]);

        $this->closeModals();
    }

    public function updateGame()
    {
        $this->validate();

        $this->selectedGame->update([
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'scoring_type' => $this->scoring_type,
            'max_players_per_game' => $this->max_players_per_game,
            'reset_frequency' => $this->reset_frequency,
            'sort_order' => $this->sort_order,
        ]);

        // Update next reset if frequency changed
        if ($this->selectedGame->wasChanged('reset_frequency')) {
            $this->selectedGame->update([
                'next_reset_at' => $this->selectedGame->calculateNextReset()
            ]);
        }

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'Game updated successfully!'
        ]);

        $this->closeModals();
    }

    public function deleteGame()
    {
        $this->selectedGame->delete();

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'Game deleted successfully!'
        ]);

        $this->closeModals();
    }

    public function toggleActive($gameId)
    {
        $game = Game::findOrFail($gameId);
        $game->update(['is_active' => !$game->is_active]);

        $status = $game->is_active ? 'activated' : 'deactivated';
        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => "Game {$status} successfully!"
        ]);
    }

    public function resetLeaderboard($gameId)
    {
        $game = Game::findOrFail($gameId);
        $game->resetLeaderboard();

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'Leaderboard reset successfully!'
        ]);
    }

    public function moveUp($gameId)
    {
        $game = Game::findOrFail($gameId);
        $previousGame = Game::where('sort_order', '<', $game->sort_order)
                           ->orderBy('sort_order', 'desc')
                           ->first();

        if ($previousGame) {
            $tempOrder = $game->sort_order;
            $game->update(['sort_order' => $previousGame->sort_order]);
            $previousGame->update(['sort_order' => $tempOrder]);
        }
    }

    public function moveDown($gameId)
    {
        $game = Game::findOrFail($gameId);
        $nextGame = Game::where('sort_order', '>', $game->sort_order)
                       ->orderBy('sort_order', 'asc')
                       ->first();

        if ($nextGame) {
            $tempOrder = $game->sort_order;
            $game->update(['sort_order' => $nextGame->sort_order]);
            $nextGame->update(['sort_order' => $tempOrder]);
        }
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->icon = '';
        $this->is_active = false;
        $this->scoring_type = 'highest';
        $this->max_players_per_game = 1;
        $this->reset_frequency = 'never';
        $this->sort_order = 0;
    }

    public function render()
    {
        $games = Game::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->ordered()
            ->paginate(10);

        return view('livewire.admin.game-management', [
            'games' => $games,
        ]);
    }
}
