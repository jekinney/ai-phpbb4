<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostEdit extends Component
{
    public Post $post;
    public string $content = '';
    public bool $isEditing = false;
    
    protected $rules = [
        'content' => 'required|string|min:3|max:10000',
    ];
    
    protected $messages = [
        'content.required' => 'Post content is required.',
        'content.min' => 'Post content must be at least 3 characters.',
        'content.max' => 'Post content cannot exceed 10,000 characters.',
    ];
    
    public function mount(Post $post)
    {
        $this->post = $post;
        $this->content = $post->content;
    }
    
    public function startEditing()
    {
        $this->authorize('update', $this->post);
        $this->isEditing = true;
        $this->content = $this->post->content;
    }
    
    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->content = $this->post->content;
        $this->resetErrorBag();
    }
    
    public function savePost()
    {
        $this->authorize('update', $this->post);
        $this->validate();
        
        $this->post->update([
            'content' => $this->content,
        ]);
        
        $this->post->processContent();
        $this->post->markAsEdited(Auth::user());
        
        $this->isEditing = false;
        
        $this->dispatch('showToast', [
            'message' => 'Post updated successfully!',
            'type' => 'success'
        ]);
    }
    
    public function deletePost()
    {
        $this->authorize('delete', $this->post);
        
        if ($this->post->is_first_post) {
            $this->dispatch('showToast', [
                'message' => 'Cannot delete the first post of a topic.',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->post->delete();
        
        $this->dispatch('showToast', [
            'message' => 'Post deleted successfully!',
            'type' => 'success'
        ]);
        
        $this->dispatch('post-deleted');
    }

    public function quotePost()
    {
        // Dispatch event to PostReply component to handle the quote
        $this->dispatch('quotePost', $this->post->id);
    }
    
    public function render()
    {
        return view('livewire.post-edit');
    }
}
