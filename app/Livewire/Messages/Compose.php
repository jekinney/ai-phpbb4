<?php

namespace App\Livewire\Messages;

use App\Models\User;
use App\Models\PersonalMessage;
use App\Models\PersonalMessageParticipant;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class Compose extends Component
{
    use AuthorizesRequests;

    public $recipients = '';
    public $subject = '';
    public $content = '';
    public $isDraft = false;
    public $replyTo = null;
    public $recipientSuggestions = [];
    public $showSuggestions = false;

    protected $rules = [
        'recipients' => 'required|string',
        'subject' => 'required|string|min:3|max:255',
        'content' => 'required|string|min:10',
    ];

    protected $messages = [
        'recipients.required' => 'Please specify at least one recipient.',
        'subject.required' => 'A subject is required.',
        'subject.min' => 'Subject must be at least 3 characters.',
        'content.required' => 'Message content is required.',
        'content.min' => 'Message must be at least 10 characters.',
    ];

    public function mount($replyTo = null, $recipient = null)
    {
        $this->authorize('send_messages');

        if ($replyTo) {
            $originalMessage = PersonalMessage::findOrFail($replyTo);
            $this->subject = 'Re: ' . preg_replace('/^Re:\s*/', '', $originalMessage->subject);
            $this->recipients = $originalMessage->sender->name;
            $this->replyTo = $replyTo;
        }

        if ($recipient) {
            $user = User::findOrFail($recipient);
            $this->recipients = $user->name;
        }
    }

    public function updatedRecipients()
    {
        if (strlen($this->recipients) >= 2) {
            $this->recipientSuggestions = User::where('name', 'like', '%' . $this->recipients . '%')
                ->where('id', '!=', auth()->id())
                ->limit(10)
                ->get(['id', 'name']);
            $this->showSuggestions = true;
        } else {
            $this->showSuggestions = false;
        }
    }

    public function selectRecipient($userId)
    {
        $user = User::findOrFail($userId);
        
        // Parse existing recipients
        $existingRecipients = array_filter(array_map('trim', explode(',', $this->recipients)));
        
        // Add new recipient if not already present
        if (!in_array($user->name, $existingRecipients)) {
            if (!empty($existingRecipients)) {
                $this->recipients .= ', ' . $user->name;
            } else {
                $this->recipients = $user->name;
            }
        }
        
        $this->showSuggestions = false;
    }

    public function saveDraft()
    {
        $this->isDraft = true;
        $this->send();
    }

    public function send()
    {
        $this->validate();

        // Parse recipients
        $recipientNames = array_filter(array_map('trim', explode(',', $this->recipients)));
        $recipientUsers = User::whereIn('name', $recipientNames)->get();

        if ($recipientUsers->count() !== count($recipientNames)) {
            $this->addError('recipients', 'One or more recipients could not be found.');
            return;
        }

        // Check if user can send messages to all recipients
        foreach ($recipientUsers as $recipient) {
            if (!$recipient->hasPermission('receive_messages')) {
                $this->addError('recipients', "User {$recipient->name} cannot receive messages.");
                return;
            }
        }

        try {
            // Create the message
            $message = PersonalMessage::create([
                'subject' => $this->subject,
                'content' => $this->content,
                'content_html' => $this->content, // For now, plain text
                'sender_id' => auth()->id(),
                'is_draft' => $this->isDraft,
                'sent_at' => $this->isDraft ? null : now(),
            ]);

            // Add participants
            foreach ($recipientUsers as $recipient) {
                PersonalMessageParticipant::create([
                    'message_id' => $message->id,
                    'user_id' => $recipient->id,
                    'type' => 'to',
                    'is_read' => false,
                ]);
            }

            // Add sender as participant for sent messages tracking
            PersonalMessageParticipant::create([
                'message_id' => $message->id,
                'user_id' => auth()->id(),
                'type' => 'to',
                'is_read' => true,
                'read_at' => now(),
            ]);

            if ($this->isDraft) {
                session()->flash('success', 'Message saved as draft.');
                return redirect()->route('messages.drafts');
            } else {
                session()->flash('success', 'Message sent successfully.');
                return redirect()->route('messages.sent');
            }
        } catch (\Exception $e) {
            $this->addError('general', 'Failed to send message. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.messages.compose');
    }
}
