<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRole = '';
    public $showBanned = false;
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedRole' => ['except' => ''],
        'showBanned' => ['except' => false],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedRole()
    {
        $this->resetPage();
    }

    public function updatingShowBanned()
    {
        $this->resetPage();
    }

    public function toggleUserBan($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->is_banned) {
            $user->update([
                'is_banned' => false,
                'banned_at' => null,
                'ban_reason' => null,
            ]);
            $this->dispatch('user-unbanned', $user->name);
        } else {
            $user->update([
                'is_banned' => true,
                'banned_at' => now(),
                'ban_reason' => 'Banned by administrator',
            ]);
            $this->dispatch('user-banned', $user->name);
        }
    }

    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedRole, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('roles.id', $this->selectedRole);
                });
            })
            ->when($this->showBanned !== null, function ($query) {
                if ($this->showBanned) {
                    $query->where('is_banned', true);
                } else {
                    $query->where('is_banned', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $roles = Role::orderBy('name')->get();

        return view('livewire.admin.user-index', [
            'users' => $users,
            'roles' => $roles,
        ])
        ->layout('layouts.admin')
        ->title('User Management');
    }
}
