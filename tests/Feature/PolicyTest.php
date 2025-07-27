<?php

namespace Tests\Feature;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'ForumSeeder']);
    }

    public function test_forum_policies_work()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role
        
        $forum = Forum::first();

        // Admin can manage forums
        $this->assertTrue($admin->can('create', Forum::class));
        $this->assertTrue($admin->can('update', $forum));
        $this->assertTrue($admin->can('delete', $forum));

        // Regular user cannot manage forums
        $this->assertFalse($user->can('create', Forum::class));
        $this->assertFalse($user->can('update', $forum));
        $this->assertFalse($user->can('delete', $forum));

        // Both can view forums
        $this->assertTrue($admin->can('view', $forum));
        $this->assertTrue($user->can('view', $forum));
    }

    public function test_topic_policies_work()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role

        $forum = Forum::first();
        $topic = Topic::factory()->create([
            'forum_id' => $forum->id,
            'user_id' => $user->id
        ]);

        // User can manage their own topic
        $this->assertTrue($user->can('update', $topic));
        $this->assertTrue($user->can('delete', $topic));

        // Admin can manage all topics
        $this->assertTrue($admin->can('update', $topic));
        $this->assertTrue($admin->can('delete', $topic));

        // Both can view topics
        $this->assertTrue($admin->can('view', $topic));
        $this->assertTrue($user->can('view', $topic));
    }

    public function test_post_policies_work()
    {
        $moderator = User::factory()->create();
        $moderator->roles()->attach(3); // moderator role
        
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role

        $forum = Forum::first();
        $topic = Topic::factory()->create(['forum_id' => $forum->id, 'user_id' => $user->id]);
        $post = Post::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'is_first_post' => false
        ]);

        // User can manage their own post
        $this->assertTrue($user->can('update', $post));
        $this->assertTrue($user->can('delete', $post));

        // Moderator can manage all posts
        $this->assertTrue($moderator->can('update', $post));
        $this->assertTrue($moderator->can('delete', $post));

        // Both can view posts
        $this->assertTrue($moderator->can('view', $post));
        $this->assertTrue($user->can('view', $post));
    }

    public function test_user_policies_work()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role
        $otherUser = User::factory()->create();
        $otherUser->roles()->attach(4); // user role

        // Admin can manage users
        $this->assertTrue($admin->can('ban', $user));
        $this->assertTrue($admin->can('editRoles', $user));

        // User can view their own profile
        $this->assertTrue($user->can('view', $user));
        $this->assertTrue($user->can('update', $user));

        // User cannot manage other users
        $this->assertFalse($user->can('ban', $otherUser));
        $this->assertFalse($user->can('editRoles', $otherUser));
        $this->assertFalse($user->can('delete', $otherUser));

        // User cannot edit their own roles (prevents privilege escalation)
        $this->assertFalse($user->can('editRoles', $user));
    }

    public function test_locked_topic_post_restrictions()
    {
        $moderator = User::factory()->create();
        $moderator->roles()->attach(3); // moderator role
        
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role

        $forum = Forum::first();
        $topic = Topic::factory()->create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'is_locked' => true
        ]);
        
        $post = Post::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'is_first_post' => false
        ]);

        // Regular users cannot reply to locked topics
        $this->assertFalse($user->can('reply', $post));

        // Moderators can reply to locked topics
        $this->assertTrue($moderator->can('reply', $post));
    }
}
