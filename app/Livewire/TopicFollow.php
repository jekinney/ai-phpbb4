<?php

namespace App\Livewire;

use App\Models\Topic;
use App\Models\TopicFollow as TopicFollowModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TopicFollow extends Component
{
    public Topic $topic;
    public bool $isFollowing = false;
    public bool $notifyReplies = true;
    public int $followersCount = 0;

    public function mount(Topic $topic)
    {
        $this->topic = $topic;
        $this->followersCount = $topic->followers_count;
        
        if (Auth::check()) {
            $this->isFollowing = Auth::user()->isFollowingTopic($topic->id);
            
            if ($this->isFollowing) {
                $follow = TopicFollowModel::where('user_id', Auth::id())
                    ->where('topic_id', $topic->id)
                    ->first();
                    
                if ($follow) {
                    $this->notifyReplies = $follow->notify_replies;
                }
            }
        }
    }

    public function toggleFollow()
    {
        if (!Auth::check()) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'You must be logged in to follow topics.'
            ]);
            return;
        }

        if ($this->isFollowing) {
            $this->unfollowTopic();
        } else {
            $this->followTopic();
        }
    }

    public function followTopic()
    {
        if (!Auth::check()) {
            return;
        }

        Auth::user()->followTopic($this->topic->id, $this->notifyReplies);
        
        $this->isFollowing = true;
        $this->followersCount++;
        
        $this->dispatch('showToast', [
            'type' => 'success',
            'message' => 'You are now following this topic!'
        ]);
    }

    public function unfollowTopic()
    {
        if (!Auth::check()) {
            return;
        }

        Auth::user()->unfollowTopic($this->topic->id);
        
        $this->isFollowing = false;
        $this->followersCount--;
        
        $this->dispatch('showToast', [
            'type' => 'info',
            'message' => 'You are no longer following this topic.'
        ]);
    }

    public function updateNotificationSettings()
    {
        if (!Auth::check() || !$this->isFollowing) {
            return;
        }

        $follow = TopicFollowModel::where('user_id', Auth::id())
            ->where('topic_id', $this->topic->id)
            ->first();

        if ($follow) {
            $follow->update(['notify_replies' => $this->notifyReplies]);
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Notification settings updated!'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.topic-follow');
    }
}
