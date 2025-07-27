<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTopicRequest;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    /**
     * Display a specific topic and its posts.
     */
    public function show(Topic $topic)
    {
        // Increment view count
        $topic->incrementViews();
        
        $posts = $topic->getPostsWithPagination();
        
        return view('topics.show', compact('topic', 'posts'));
    }

    /**
     * Show the form for creating a new topic.
     */
    public function create(Forum $forum)
    {
        return view('topics.create', compact('forum'));
    }

    /**
     * Store a newly created topic.
     */
    public function store(CreateTopicRequest $request, Forum $forum)
    {
        $topic = null;
        
        DB::transaction(function () use ($request, $forum, &$topic) {
            // Create the topic
            $topic = Topic::create([
                'forum_id' => $forum->id,
                'user_id' => Auth::id(),
                'title' => $request->title,
                'is_sticky' => false,
                'is_locked' => false,
            ]);

            // Create the first post
            $post = Post::createPost([
                'topic_id' => $topic->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'is_first_post' => true,
                'user_ip' => $request->ip(),
            ]);

            // Update topic with first post info
            $topic->update([
                'last_post_id' => $post->id,
                'last_post_user_id' => Auth::id(),
                'last_post_at' => $post->created_at,
                'posts_count' => 1,
            ]);
        });

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Topic created successfully!');
    }

    /**
     * Show the form for editing a topic.
     */
    public function edit(Topic $topic)
    {
        $this->authorize('update', $topic);
        
        return view('topics.edit', compact('topic'));
    }

    /**
     * Update the specified topic.
     */
    public function update(CreateTopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        
        $topic->update([
            'title' => $request->title,
        ]);

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Topic updated successfully!');
    }

    /**
     * Remove the specified topic.
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('delete', $topic);
        
        $forum = $topic->forum;
        $topic->delete();

        return redirect()->route('forums.show', $forum)
            ->with('success', 'Topic deleted successfully!');
    }
}
