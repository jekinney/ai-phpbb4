<?php

namespace App\Livewire;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopicCreate extends Component
{
    public Forum $forum;
    public $title = '';
    public $content = '';

    protected $rules = [
        'title' => 'required|string|min:3|max:255|regex:/^[\w\s\-_.!?()]+$/u',
        'content' => 'required|string|min:10|max:10000',
    ];

    protected $messages = [
        'title.required' => 'The topic title is required.',
        'title.min' => 'The topic title must be at least 3 characters.',
        'title.max' => 'The topic title cannot exceed 255 characters.',
        'title.regex' => 'The topic title contains invalid characters.',
        'content.required' => 'The post content is required.',
        'content.min' => 'The post content must be at least 10 characters.',
        'content.max' => 'The post content cannot exceed 10,000 characters.',
    ];

    public function mount(Forum $forum)
    {
        $this->forum = $forum;
    }

    public function submit()
    {
        $this->validate();

        $topic = null;

        DB::transaction(function () use (&$topic) {
            // Create the topic
            $topic = Topic::create([
                'forum_id' => $this->forum->id,
                'user_id' => Auth::id(),
                'title' => $this->title,
                'is_sticky' => false,
                'is_locked' => false,
            ]);

            // Create the first post
            $post = Post::createPost([
                'topic_id' => $topic->id,
                'user_id' => Auth::id(),
                'content' => $this->content,
                'is_first_post' => true,
                'user_ip' => request()->ip(),
            ]);

            // Update topic with first post info
            $topic->update([
                'last_post_id' => $post->id,
                'last_post_user_id' => Auth::id(),
                'last_post_at' => $post->created_at,
                'posts_count' => 1,
            ]);
        });

        session()->flash('success', 'Topic created successfully!');
        return redirect()->route('topics.show', $topic);
    }

    public function render()
    {
        return view('livewire.topic-create')->title('Create New Topic');
    }
}
