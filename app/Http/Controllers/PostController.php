<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created post.
     */
    public function store(CreatePostRequest $request, Topic $topic)
    {
        if ($topic->is_locked) {
            return back()->with('error', 'This topic is locked and cannot accept new posts.');
        }

        Post::createPost([
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'user_ip' => $request->ip(),
        ]);

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Post created successfully!');
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post.
     */
    public function update(CreatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        
        $post->update([
            'content' => $request->content,
        ]);
        
        $post->processContent();
        $post->markAsEdited(Auth::user());

        return redirect()->route('topics.show', $post->topic)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        $topic = $post->topic;
        
        // Prevent deletion of the first post
        if ($post->is_first_post) {
            return back()->with('error', 'Cannot delete the first post of a topic.');
        }
        
        $post->delete();

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Post deleted successfully!');
    }
}
