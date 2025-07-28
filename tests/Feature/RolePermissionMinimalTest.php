<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionMinimalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create only the absolute minimum data needed for tests
        $this->createBasicTestData();
    }
    
    private function createBasicTestData(): void
    {
        // Create just a few essential permissions
        $permissions = [
            Permission::create(['name' => 'manage_forums', 'display_name' => 'Manage Forums', 'category' => 'forum']),
            Permission::create(['name' => 'create_posts', 'display_name' => 'Create Posts', 'category' => 'post']),
            Permission::create(['name' => 'moderate_posts', 'display_name' => 'Moderate Posts', 'category' => 'post']),
        ];

        // Create just two roles
        $adminRole = Role::create(['name' => 'administrator', 'display_name' => 'Administrator', 'level' => 900, 'is_default' => false]);
        $userRole = Role::create(['name' => 'user', 'display_name' => 'User', 'level' => 100, 'is_default' => true]);

        // Assign permissions
        $adminRole->permissions()->attach([$permissions[0]->id, $permissions[1]->id, $permissions[2]->id]);
        $userRole->permissions()->attach([$permissions[1]->id]);
    }

    /** @test */
    public function roles_have_correct_permissions()
    {
        $adminRole = Role::where('name', 'administrator')->first();
        $userRole = Role::where('name', 'user')->first();
        
        $this->assertNotNull($adminRole);
        $this->assertNotNull($userRole);
        $this->assertEquals(3, $adminRole->permissions->count());
        $this->assertEquals(1, $userRole->permissions->count());
    }

    /** @test */
    public function users_can_be_assigned_roles()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'administrator')->first();
        
        $user->assignRole($role);
        
        $this->assertTrue($user->hasRole('administrator'));
    }

    /** @test */
    public function users_inherit_permissions_from_roles()
    {
        $user = User::factory()->create();
        $adminRole = Role::where('name', 'administrator')->first();
        
        $user->assignRole($adminRole);
        
        $this->assertTrue($user->can('manage_forums'));
        $this->assertTrue($user->can('create_posts'));
        $this->assertTrue($user->can('moderate_posts'));
    }

    /** @test */
    public function users_without_permissions_cannot_perform_actions()
    {
        $user = User::factory()->create();
        $userRole = Role::where('name', 'user')->first();
        
        $user->assignRole($userRole);
        
        $this->assertTrue($user->can('create_posts'));
        $this->assertFalse($user->can('manage_forums'));
        $this->assertFalse($user->can('moderate_posts'));
    }

    /** @test */
    public function roles_can_be_removed_from_users()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'administrator')->first();
        
        $user->assignRole($role);
        $this->assertTrue($user->hasRole('administrator'));
        
        $user->removeRole($role);
        $this->assertFalse($user->hasRole('administrator'));
    }
}
