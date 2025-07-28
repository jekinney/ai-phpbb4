<?php

namespace App\Livewire\Messages;

use App\Models\PersonalMessage;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests;

    public PersonalMessage $message;
    public $showReplyForm = false;
    public $replyContent = '';

    protected $rules = [
        'replyContent' => 'required|string|min:10',
    ];

    public function mount(PersonalMessage $message)
    {
        $this->message = $message;
        
        // Check if user has access to this message
        if (!$message->participants()->where('user_id', auth()->id())->exists() && 
            $message->sender_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this message.');
        }

        // Mark as read if it's unread
        if (!$message->isReadBy(auth()->user())) {
            $message->markAsReadBy(auth()->user());
        }
    }

    public function toggleReplyForm()
    {
        $this->authorize('send_messages');
        $this->showReplyForm = !$this->showReplyForm;
        
        if (!$this->showReplyForm) {
            $this->replyContent = '';
        }
    }

    public function sendReply()
    {
        $this->authorize('send_messages');
        $this->validate();

        try {
            // Create reply message
            $replyMessage = PersonalMessage::create([
                'subject' => 'Re: ' . preg_replace('/^Re:\s*/', '', $this->message->subject),
                'content' => $this->replyContent,
                'content_html' => $this->replyContent,
                'sender_id' => auth()->id(),
                'is_draft' => false,
                'sent_at' => now(),
            ]);

            // Add original sender as recipient (if not the current user)
            if ($this->message->sender_id !== auth()->id()) {
                \App\Models\PersonalMessageParticipant::create([
                    'message_id' => $replyMessage->id,
                    'user_id' => $this->message->sender_id,
                    'type' => 'to',
                    'is_read' => false,
                ]);
            }

            // Add sender as participant for tracking
            \App\Models\PersonalMessageParticipant::create([
                'message_id' => $replyMessage->id,
                'user_id' => auth()->id(),
                'type' => 'to',
                'is_read' => true,
                'read_at' => now(),
            ]);

            $this->replyContent = '';
            $this->showReplyForm = false;
            
            session()->flash('success', 'Reply sent successfully.');
            return redirect()->route('messages.show', $replyMessage);
        } catch (\Exception $e) {
            $this->addError('replyContent', 'Failed to send reply. Please try again.');
        }
    }

    public function deleteMessage()
    {
        $this->authorize('delete_own_messages');
        
        $this->message->markAsDeletedBy(auth()->user());
        
        session()->flash('success', 'Message deleted.');
        return redirect()->route('messages.inbox');
    }

    public function getIsReadProperty()
    {
        return $this->message->isReadBy(auth()->user());
    }

    public function getCanReplyProperty()
    {
        return auth()->user()->hasPermission('send_messages') && 
               $this->message->sender_id !== auth()->id();
    }

    public function getCanDeleteProperty()
    {
        return auth()->user()->hasPermission('delete_own_messages') ||
               (auth()->user()->hasPermission('delete_any_message') && 
                auth()->user()->hasPermission('view_all_messages'));
    }

    public function render()
    {
        return view('livewire.messages.show');
    }
}
