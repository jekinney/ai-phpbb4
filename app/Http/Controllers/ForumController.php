<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /**
     * Display the forums index page.
     */
    public function index()
    {
        $categories = Category::getForumsIndex();
        
        return view('forums.index', compact('categories'));
    }

    /**
     * Display a specific forum and its topics.
     */
    public function show(Forum $forum)
    {
        $topics = $forum->getTopicsWithPagination();
        
        return view('forums.show', compact('forum', 'topics'));
    }
}
