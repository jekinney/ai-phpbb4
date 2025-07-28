<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Topic;
use App\Models\FileAttachment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostReply extends Component
{
    use WithFileUploads;

    public Topic $topic;
    public $content = '';
    public $showForm = false;
    public $quotedPostId = null;
    public $quotedContent = '';
    public $quotedAuthor = '';
    public $attachments = [];

    protected $rules = [
        'content' => 'required|string|min:3|max:10000',
    ];

    protected $messages = [
        'content.required' => 'The post content is required.',
        'content.min' => 'The post content must be at least 3 characters.',
        'content.max' => 'The post content cannot exceed 10,000 characters.',
    ];

    protected $listeners = [
        'quotePost' => 'handleQuotePost',
    ];

    public function mount(Topic $topic)
    {
        $this->topic = $topic;
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function handleQuotePost($postId)
    {
        $post = Post::with('user')->find($postId);
        
        if (!$post) {
            return;
        }

        $this->quotedPostId = $postId;
        $this->quotedContent = $post->content;
        $this->quotedAuthor = $post->user->name;
        $this->showForm = true;
        
        // Scroll to reply form
        $this->dispatch('scrollToReplyForm');
    }

    public function clearQuote()
    {
        $this->quotedPostId = null;
        $this->quotedContent = '';
        $this->quotedAuthor = '';
    }

    public function resetForm()
    {
        $this->content = '';
        $this->attachments = [];
        $this->clearQuote();
        $this->resetErrorBag();
    }

    public function submit()
    {
        if ($this->topic->is_locked) {
            $this->addError('locked', 'This topic is locked and cannot accept new posts.');
            return;
        }

        $this->validate();

        // Prepare content with quote if present
        $finalContent = $this->content;
        if ($this->quotedPostId && $this->quotedContent) {
            $quoteHeader = "[quote={$this->quotedAuthor}]";
            $quoteFooter = "[/quote]";
            $finalContent = $quoteHeader . "\n" . $this->quotedContent . "\n" . $quoteFooter . "\n\n" . $this->content;
        }

        Post::createPost([
            'topic_id' => $this->topic->id,
            'user_id' => Auth::id(),
            'content' => $finalContent,
            'user_ip' => request()->ip(),
        ]);

        $this->resetForm();
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
