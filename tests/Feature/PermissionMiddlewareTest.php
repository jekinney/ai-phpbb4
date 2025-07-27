<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
    }

    public function test_super_admin_can_access_everything()
    {
        $superAdmin = User::where('email', 'admin@example.com')->first();
        
        $this->assertTrue($superAdmin->can('view_forums'));
        $this->assertTrue($superAdmin->can('manage_users'));
        $this->assertTrue($superAdmin->can('manage_forums'));
        $this->assertTrue($superAdmin->can('delete_all_posts'));
        $this->assertTrue($superAdmin->can('access_admin_panel'));
    }

    public function test_regular_user_has_limited_permissions()
    {
        $user = User::factory()->create();
        // Manually assign user role (since automatic assignment is disabled in tests)
        $user->roles()->attach(4); // user role ID
        
        $this->assertTrue($user->can('view_forums'));
        $this->assertTrue($user->can('create_topics'));
        $this->assertTrue($user->can('view_posts'));
        $this->assertTrue($user->can('create_posts'));
        $this->assertFalse($user->can('manage_users'));
        $this->assertFalse($user->can('manage_forums'));
        $this->assertFalse($user->can('delete_all_posts'));
    }

    public function test_banned_user_cannot_do_anything()
    {
        $user = User::factory()->create();
        $user->roles()->attach(5); // banned role ID
        
        $this->assertFalse($user->can('view_forums'));
        $this->assertFalse($user->can('create_topics'));
        $this->assertFalse($user->can('view_posts'));
    }

    public function test_user_can_be_banned()
    {
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role
        
        $this->assertTrue($user->can('view_forums'));
        
        // Ban the user
        $user->ban('Spam posting');
        
        $this->assertTrue($user->is_banned);
        $this->assertNotNull($user->banned_at);
        $this->assertEquals('Spam posting', $user->ban_reason);
        $this->assertFalse($user->can('view_forums'));
    }

    public function test_user_can_be_unbanned()
    {
        $user = User::factory()->create();
        $user->ban('Test ban');
        
        $this->assertTrue($user->is_banned);
        $this->assertFalse($user->can('view_forums'));
        
        $user->unban();
        
        $this->assertFalse($user->is_banned);
        $this->assertNull($user->banned_at);
        $this->assertNull($user->ban_reason);
        // Should have default role assigned again
        $this->assertTrue($user->hasRole('user'));
    }
}
