<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleAclTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_user_exists()
    {
        // Run the config-based seeder
        $this->artisan('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
        
        $superAdmin = User::where('email', 'admin@example.com')->first();
        
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->hasRole('super_admin'));
    }

    public function test_roles_exist()
    {
        $this->artisan('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
        
        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
        $this->assertDatabaseHas('roles', ['name' => 'administrator']);
        $this->assertDatabaseHas('roles', ['name' => 'moderator']);
        $this->assertDatabaseHas('roles', ['name' => 'user']);
        $this->assertDatabaseHas('roles', ['name' => 'banned']);
    }

    public function test_permissions_exist()
    {
        $this->artisan('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
        
        // Test some key permissions exist
        $this->assertDatabaseHas('permissions', ['name' => 'view_forums']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage_forums']);
        $this->assertDatabaseHas('permissions', ['name' => 'create_topics']);
        $this->assertDatabaseHas('permissions', ['name' => 'access_admin_panel']);
        $this->assertDatabaseHas('permissions', ['name' => 'ban_users']);
    }

    public function test_role_permissions_are_assigned()
    {
        $this->artisan('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
        
        $superAdmin = Role::where('name', 'super_admin')->first();
        $user = Role::where('name', 'user')->first();
        
        // Super admin should have many permissions
        $this->assertGreaterThan(30, $superAdmin->permissions()->count());
        
        // Regular user should have basic permissions
        $this->assertGreaterThan(5, $user->permissions()->count());
        $this->assertLessThan(15, $user->permissions()->count());
    }

    public function test_super_admin_has_role_assigned()
    {
        $this->artisan('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
        
        $this->assertDatabaseHas('user_roles', [
            'user_id' => 1, // super admin user
            'role_id' => 1  // super admin role
        ]);
    }
}
