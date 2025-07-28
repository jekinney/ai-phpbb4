<?php

namespace App\Livewire\Messages;

use App\Models\PersonalMessage;
use App\Models\PersonalMessageParticipant;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Inbox extends Component
{
    use WithPagination, AuthorizesRequests;

    public $activeTab = 'inbox';
    public $search = '';
    public $selectedMessages = [];
    public $selectAll = false;

    protected $queryString = [
        'activeTab' => ['except' => 'inbox'],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('receive_messages');
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedMessages = $this->getMessages()->pluck('id')->toArray();
        } else {
            $this->selectedMessages = [];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->selectedMessages = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function markAsRead($messageId)
    {
        $message = PersonalMessage::findOrFail($messageId);
        
        if (!$message->isReadBy(auth()->user())) {
            $message->markAsReadBy(auth()->user());
        }
        
        $this->dispatch('message-read', $messageId);
    }

    public function markSelectedAsRead()
    {
        if (!empty($this->selectedMessages)) {
            foreach ($this->selectedMessages as $messageId) {
                $message = PersonalMessage::findOrFail($messageId);
                $message->markAsReadBy(auth()->user());
            }
            
            $this->selectedMessages = [];
            $this->selectAll = false;
            $this->dispatch('messages-read');
        }
    }

    public function deleteSelected()
    {
        $this->authorize('delete_own_messages');
        
        if (!empty($this->selectedMessages)) {
            foreach ($this->selectedMessages as $messageId) {
                $message = PersonalMessage::findOrFail($messageId);
                $message->markAsDeletedBy(auth()->user());
            }
            
            $this->selectedMessages = [];
            $this->selectAll = false;
            $this->dispatch('messages-deleted');
        }
    }

    public function deleteMessage($messageId)
    {
        $this->authorize('delete_own_messages');
        
        $message = PersonalMessage::findOrFail($messageId);
        $message->markAsDeletedBy(auth()->user());
        
        $this->dispatch('message-deleted', $messageId);
    }

    protected function getMessages()
    {
        $query = PersonalMessage::with(['sender', 'participants.user'])
            ->forUser(auth()->user())
            ->notDrafts()
            ->orderBy('created_at', 'desc');

        if ($this->activeTab === 'inbox') {
            $query->receivedBy(auth()->user());
        } elseif ($this->activeTab === 'sent') {
            $query->sentBy(auth()->user());
        } elseif ($this->activeTab === 'unread') {
            $query->unreadForUser(auth()->user());
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%')
                  ->orWhereHas('sender', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query;
    }

    public function getUnreadCountProperty()
    {
        return auth()->user()->unread_messages_count;
    }

    public function render()
    {
        $messages = $this->getMessages()->paginate(20);
        
        $stats = [
            'total' => PersonalMessage::forUser(auth()->user())->notDrafts()->count(),
            'unread' => PersonalMessage::unreadForUser(auth()->user())->count(),
            'sent' => PersonalMessage::sentBy(auth()->user())->notDrafts()->count(),
        ];

        return view('livewire.messages.inbox', [
            'messages' => $messages,
            'stats' => $stats,
        ]);
    }
}
