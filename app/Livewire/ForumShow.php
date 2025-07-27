<?php

namespace App\Livewire;

use App\Models\Forum;
use Livewire\Component;
use Livewire\WithPagination;

class ForumShow extends Component
{
    use WithPagination;

    public Forum $forum;
    public $perPage = 20;

    public function mount(Forum $forum)
    {
        $this->forum = $forum;
    }

    public function render()
    {
        $topics = $this->forum->topics()
            ->with(['user', 'lastPost.user'])
            ->orderBy('is_sticky', 'desc')
            ->orderBy('last_post_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.forum-show', [
            'topics' => $topics
        ])->title($this->forum->name);
    }
}
