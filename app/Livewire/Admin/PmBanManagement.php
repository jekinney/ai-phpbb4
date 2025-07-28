<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PmBanManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showBanModal = false;
    public $showUnbanModal = false;
    public $selectedUser = null;
    public $banReason = '';
    public $banDuration = '';
    public $banType = 'permanent'; // permanent, temporary

    protected $rules = [
        'banReason' => 'required|string|min:10|max:500',
        'banDuration' => 'nullable|required_if:banType,temporary|integer|min:1|max:365',
    ];

    protected $messages = [
        'banReason.required' => 'A ban reason is required.',
        'banReason.min' => 'Ban reason must be at least 10 characters.',
        'banDuration.required_if' => 'Duration is required for temporary bans.',
        'banDuration.integer' => 'Duration must be a number of days.',
        'banDuration.min' => 'Minimum ban duration is 1 day.',
        'banDuration.max' => 'Maximum ban duration is 365 days.',
    ];

    public function mount()
    {
        $this->authorize('manage_pm_bans');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openBanModal($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->banReason = '';
        $this->banDuration = '';
        $this->banType = 'permanent';
        $this->showBanModal = true;
    }

    public function openUnbanModal($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->showUnbanModal = true;
    }

    public function closeBanModal()
    {
        $this->showBanModal = false;
        $this->selectedUser = null;
        $this->reset(['banReason', 'banDuration', 'banType']);
        $this->resetErrorBag();
    }

    public function closeUnbanModal()
    {
        $this->showUnbanModal = false;
        $this->selectedUser = null;
    }

    public function banUser()
    {
        $this->validate();

        if (!$this->selectedUser) {
            return;
        }

        // Prevent banning super admins
        if ($this->selectedUser->hasRole('super_admin')) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Cannot ban super administrators.'
            ]);
            return;
        }

        // Calculate expiry date for temporary bans
        $expiresAt = null;
        if ($this->banType === 'temporary' && $this->banDuration) {
            $expiresAt = now()->addDays($this->banDuration);
        }

        $this->selectedUser->pmBan(Auth::user(), $this->banReason, $expiresAt);

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => "User {$this->selectedUser->name} has been banned from the PM system."
        ]);

        $this->closeBanModal();
    }

    public function unbanUser()
    {
        if (!$this->selectedUser) {
            return;
        }

        $this->selectedUser->removePmBan();

        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => "User {$this->selectedUser->name} has been unbanned from the PM system."
        ]);

        $this->closeUnbanModal();
    }

    public function render()
    {
        $users = User::when($this->search, function($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->with('pmBannedBy')
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.pm-ban-management', [
            'users' => $users
        ])->title('PM Ban Management');
    }
}
