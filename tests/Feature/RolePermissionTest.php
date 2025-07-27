<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run the seeder but with minimal overhead
        $this->artisan('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
    }

    /** @test */
    public function basic_roles_exist()
    {
        $roles = ['super_admin', 'administrator', 'moderator', 'user', 'banned'];
        
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            $this->assertNotNull($role, "Role {$roleName} should exist");
        }
    }

    /** @test */
    public function basic_permissions_exist()
    {
        $permissions = ['manage_forums', 'create_posts', 'moderate_posts'];
        
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            $this->assertNotNull($permission, "Permission {$permissionName} should exist");
        }
    }

    /** @test */
    public function user_can_be_assigned_role()
    {
        $user = User::factory()->create();
        $userRole = Role::where('name', 'user')->first();
        
        $user->assignRole($userRole);
        
        $this->assertTrue($user->hasRole('user'));
    }

    /** @test */
    public function super_admin_exists()
    {
        $superUser = User::where('email', 'admin@example.com')->first();
        
        $this->assertNotNull($superUser);
        $this->assertTrue($superUser->hasRole('super_admin'));
    }

    /** @test */
    public function role_hierarchy_is_correct()
    {
        $superAdmin = Role::where('name', 'super_admin')->first();
        $admin = Role::where('name', 'administrator')->first();
        $moderator = Role::where('name', 'moderator')->first();
        $user = Role::where('name', 'user')->first();
        $banned = Role::where('name', 'banned')->first();
        
        $this->assertEquals(1000, $superAdmin->level);
        $this->assertEquals(900, $admin->level);
        $this->assertEquals(500, $moderator->level);
        $this->assertEquals(100, $user->level);
        $this->assertEquals(0, $banned->level);
    }
}