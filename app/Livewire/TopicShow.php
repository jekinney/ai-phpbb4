<?php

namespace App\Livewire;

use App\Models\Topic;
use Livewire\Component;
use Livewire\WithPagination;

class TopicShow extends Component
{
    use WithPagination;

    public Topic $topic;
    public $perPage = 15;
    
    protected $listeners = [
        'post-deleted' => 'handlePostDeleted'
    ];

    public function mount(Topic $topic)
    {
        $this->topic = $topic;
        // Increment view count
        $this->topic->incrementViews();
    }
    
    public function handlePostDeleted()
    {
        // Refresh the page to remove the deleted post
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $posts = $this->topic->posts()
            ->with(['user', 'editedBy'])
            ->orderBy('created_at')
            ->paginate($this->perPage);

        return view('livewire.topic-show', [
            'posts' => $posts
        ])->title($this->topic->title);
    }
}
