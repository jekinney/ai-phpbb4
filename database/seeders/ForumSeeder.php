<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $generalCategory = Category::firstOrCreate([
            'name' => 'General Discussion',
        ], [
            'description' => 'General topics and community discussions',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $techCategory = Category::firstOrCreate([
            'name' => 'Technology',
        ], [
            'description' => 'Technology-related discussions',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $supportCategory = Category::firstOrCreate([
            'name' => 'Support',
        ], [
            'description' => 'Get help and support from the community',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // Create forums
        $introductions = Forum::create([
            'category_id' => $generalCategory->id,
            'name' => 'Introductions',
            'description' => 'Introduce yourself to the community',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $generalChat = Forum::create([
            'category_id' => $generalCategory->id,
            'name' => 'General Chat',
            'description' => 'General discussions about anything and everything',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $webDev = Forum::create([
            'category_id' => $techCategory->id,
            'name' => 'Web Development',
            'description' => 'Discussions about web development, frameworks, and tools',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $programming = Forum::create([
            'category_id' => $techCategory->id,
            'name' => 'Programming',
            'description' => 'General programming discussions and help',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $helpDesk = Forum::create([
            'category_id' => $supportCategory->id,
            'name' => 'Help Desk',
            'description' => 'Get help with technical issues',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create a demo user if one doesn't exist
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create some sample topics and posts
        $welcomeTopic = Topic::create([
            'forum_id' => $introductions->id,
            'user_id' => $demoUser->id,
            'title' => 'Welcome to our Forum!',
            'is_sticky' => true,
        ]);

        $welcomePost = Post::create([
            'topic_id' => $welcomeTopic->id,
            'user_id' => $demoUser->id,
            'content' => "Welcome to our community forum!\n\nThis is a place where we can discuss various topics, share knowledge, and help each other out. Please feel free to introduce yourself and let us know what brings you here.\n\nWe're excited to have you as part of our community!",
            'is_first_post' => true,
        ]);
        $welcomePost->processContent();

        // Update topic stats
        $welcomeTopic->update([
            'posts_count' => 1,
            'last_post_id' => $welcomePost->id,
            'last_post_user_id' => $demoUser->id,
            'last_post_at' => $welcomePost->created_at,
        ]);

        // Create a Laravel discussion topic
        $laravelTopic = Topic::create([
            'forum_id' => $webDev->id,
            'user_id' => $demoUser->id,
            'title' => 'Getting Started with Laravel Framework',
        ]);

        $laravelPost = Post::create([
            'topic_id' => $laravelTopic->id,
            'user_id' => $demoUser->id,
            'content' => "Laravel is an amazing PHP framework that makes web development a joy!\n\nSome of the features I love about Laravel:\n- Eloquent ORM for database interactions\n- Blade templating engine\n- Artisan command-line tool\n- Built-in authentication\n- Migration system for database schema management\n\nWhat are your favorite Laravel features? Any tips for beginners?",
            'is_first_post' => true,
        ]);
        $laravelPost->processContent();

        $laravelTopic->update([
            'posts_count' => 1,
            'last_post_id' => $laravelPost->id,
            'last_post_user_id' => $demoUser->id,
            'last_post_at' => $laravelPost->created_at,
        ]);

        // Create a programming help topic
        $helpTopic = Topic::create([
            'forum_id' => $programming->id,
            'user_id' => $demoUser->id,
            'title' => 'Understanding Object-Oriented Programming',
        ]);

        $helpPost = Post::create([
            'topic_id' => $helpTopic->id,
            'user_id' => $demoUser->id,
            'content' => "I'm trying to understand the core concepts of Object-Oriented Programming (OOP). Can someone explain the main principles?\n\nFrom what I understand so far:\n1. Encapsulation - bundling data and methods together\n2. Inheritance - creating new classes based on existing ones\n3. Polymorphism - objects of different types responding to the same interface\n4. Abstraction - hiding complex implementation details\n\nAny examples or resources you'd recommend for learning OOP better?",
            'is_first_post' => true,
        ]);
        $helpPost->processContent();

        $helpTopic->update([
            'posts_count' => 1,
            'last_post_id' => $helpPost->id,
            'last_post_user_id' => $demoUser->id,
            'last_post_at' => $helpPost->created_at,
        ]);

        // Update forum statistics
        foreach ([$introductions, $generalChat, $webDev, $programming, $helpDesk] as $forum) {
            $forum->updateStats();
        }
    }
}
