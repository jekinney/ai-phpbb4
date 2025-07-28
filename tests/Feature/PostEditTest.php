<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostEditTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupBasicPermissions();
    }

    /** @test */
    public function user_can_edit_their_own_post()
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);
        $topic = Topic::factory()->create(['forum_id' => $forum->id, 'user_id' => $user->id]);
        $post = Post::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'content' => 'Original content',
            'is_first_post' => false
        ]);

        $this->actingAs($user);

        Livewire::test('post-edit', ['post' => $post])
            ->call('startEditing')
            ->assertSet('isEditing', true)
            ->set('content', 'Updated content')
            ->call('savePost')
            ->assertSet('isEditing', false)
            ->assertDispatched('showToast');

        $this->assertEquals('Updated content', $post->fresh()->content);
        $this->assertNotNull($post->fresh()->edited_at);
        $this->assertEquals($user->id, $post->fresh()->edited_by);
    }

    /** @test */
    public function user_can_delete_their_own_post()
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);
        $topic = Topic::factory()->create(['forum_id' => $forum->id, 'user_id' => $user->id]);
        $post = Post::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'is_first_post' => false
        ]);

        $this->actingAs($user);

        Livewire::test('post-edit', ['post' => $post])
            ->call('deletePost')
            ->assertDispatched('showToast')
            ->assertDispatched('post-deleted');

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function user_cannot_delete_first_post_of_topic()
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);
        $topic = Topic::factory()->create(['forum_id' => $forum->id, 'user_id' => $user->id]);
        $post = Post::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
            'is_first_post' => true
        ]);

        $this->actingAs($user);

        Livewire::test('post-edit', ['post' => $post])
            ->call('deletePost')
            ->assertDispatched('showToast');

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    /** @test */
    public function user_cannot_edit_others_posts_without_permission()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->assignRole('member');
        $otherUser->assignRole('member');
        
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);
        $topic = Topic::factory()->create(['forum_id' => $forum->id, 'user_id' => $otherUser->id]);
        $post = Post::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $otherUser->id,
            'is_first_post' => false
        ]);

        $this->actingAs($user);

        // This should trigger authorization failure
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        
        Livewire::test('post-edit', ['post' => $post])
            ->call('startEditing');
    }

    private function setupBasicPermissions()
    {
        // Create basic permissions and roles
        \Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
    }
}
