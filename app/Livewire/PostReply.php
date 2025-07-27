<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostReply extends Component
{
    public Topic $topic;
    public $content = '';
    public $showForm = false;

    protected $rules = [
        'content' => 'required|string|min:3|max:10000',
    ];

    protected $messages = [
        'content.required' => 'The post content is required.',
        'content.min' => 'The post content must be at least 3 characters.',
        'content.max' => 'The post content cannot exceed 10,000 characters.',
    ];

    public function mount(Topic $topic)
    {
        $this->topic = $topic;
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->content = '';
            $this->resetErrorBag();
        }
    }

    public function submit()
    {
        if ($this->topic->is_locked) {
            $this->addError('locked', 'This topic is locked and cannot accept new posts.');
            return;
        }

        $this->validate();

        Post::createPost([
            'topic_id' => $this->topic->id,
            'user_id' => Auth::id(),
            'content' => $this->content,
            'user_ip' => request()->ip(),
        ]);

        $this->content = '';
        $this->showForm = false;
        $this->dispatch('postAdded');
        session()->flash('success', 'Post created successfully!');
        
        // Refresh the parent component
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.post-reply');
    }
}
