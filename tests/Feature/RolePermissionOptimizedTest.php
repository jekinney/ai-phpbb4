<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionOptimizedTest extends TestCase
{
    use RefreshDatabase;

    protected static bool $seeded = false;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations first
        $this->artisan('migrate:fresh');
        
        // Only seed once for all tests to save memory
        if (!self::$seeded) {
            $this->seed(\Database\Seeders\ConfigBasedRolePermissionSeeder::class);
            self::$seeded = true;
        }
    }

    /** @test */
    public function it_can_create_roles_with_permissions()
    {
        $role = Role::where('name', 'administrator')->first();
        
        $this->assertNotNull($role);
        $this->assertTrue($role->permissions->count() > 0);
    }

    /** @test */
    public function it_can_assign_roles_to_users()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'moderator')->first();
        
        $user->assignRole($role);
        
        $this->assertTrue($user->hasRole('moderator'));
        $this->assertTrue($user->roles->contains($role));
    }

    /** @test */
    public function it_can_check_user_permissions()
    {
        $user = User::factory()->create();
        $adminRole = Role::where('name', 'administrator')->first();
        
        $user->assignRole($adminRole);
        
        $this->assertTrue($user->can('manage_forums'));
        $this->assertTrue($user->can('manage_users'));
        $this->assertTrue($user->can('delete_all_posts'));
    }

    /** @test */
    public function moderator_has_limited_permissions()
    {
        $user = User::factory()->create();
        $modRole = Role::where('name', 'moderator')->first();
        
        $user->assignRole($modRole);
        
        $this->assertTrue($user->can('moderate_topics'));
        $this->assertTrue($user->can('moderate_posts'));
        $this->assertFalse($user->can('manage_users'));
        $this->assertFalse($user->can('manage_forums'));
    }

    /** @test */
    public function regular_user_has_basic_permissions()
    {
        $user = User::factory()->create();
        $userRole = Role::where('name', 'user')->first();
        
        $user->assignRole($userRole);
        
        $this->assertTrue($user->can('create_topics'));
        $this->assertTrue($user->can('create_posts'));
        $this->assertFalse($user->can('moderate_topics'));
        $this->assertFalse($user->can('delete_all_posts'));
    }

    /** @test */
    public function banned_user_has_no_permissions()
    {
        $user = User::factory()->create();
        $bannedRole = Role::where('name', 'banned')->first();
        
        $user->assignRole($bannedRole);
        
        $this->assertFalse($user->can('create_topics'));
        $this->assertFalse($user->can('create_posts'));
        $this->assertFalse($user->can('view_forums'));
    }

    /** @test */
    public function super_user_exists_and_has_all_permissions()
    {
        $superUser = User::where('email', 'admin@example.com')->first();
        
        $this->assertNotNull($superUser);
        $this->assertTrue($superUser->hasRole('super_admin'));
        
        // Check some key permissions
        $this->assertTrue($superUser->can('manage_forums'));
        $this->assertTrue($superUser->can('manage_users'));
        $this->assertTrue($superUser->can('access_admin_panel'));
    }

    /** @test */
    public function user_can_have_multiple_roles()
    {
        $user = User::factory()->create();
        $userRole = Role::where('name', 'user')->first();
        $modRole = Role::where('name', 'moderator')->first();
        
        $user->assignRole($userRole);
        $user->assignRole($modRole);
        
        $this->assertTrue($user->hasRole('user'));
        $this->assertTrue($user->hasRole('moderator'));
        $this->assertTrue($user->can('create_topics')); // From user role
        $this->assertTrue($user->can('moderate_topics')); // From moderator role
    }

    /** @test */
    public function it_can_remove_roles_from_users()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'moderator')->first();
        
        $user->assignRole($role);
        $this->assertTrue($user->hasRole('moderator'));
        
        $user->removeRole($role);
        $this->assertFalse($user->hasRole('moderator'));
    }

    /** @test */
    public function permissions_are_managed_through_config()
    {
        // Verify that permissions come from config
        $configPermissions = collect(config('acl.permissions'))->flatten();
        $dbPermissions = Permission::pluck('name');
        
        // All config permissions should exist in database
        foreach ($configPermissions->keys() as $permissionName) {
            $this->assertTrue(
                $dbPermissions->contains($permissionName),
                "Permission '{$permissionName}' from config should exist in database"
            );
        }
        
        // Should have the expected number of permissions
        $this->assertEquals($configPermissions->count(), $dbPermissions->count());
    }
}
