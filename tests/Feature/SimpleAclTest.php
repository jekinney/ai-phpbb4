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
        // Run the simple seeder
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
        
        $superAdmin = User::where('email', 'admin@example.com')->first();
        
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->hasRole('super_admin'));
    }

    public function test_roles_exist()
    {
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
        
        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
        $this->assertDatabaseHas('roles', ['name' => 'administrator']);
        $this->assertDatabaseHas('roles', ['name' => 'moderator']);
        $this->assertDatabaseHas('roles', ['name' => 'user']);
        $this->assertDatabaseHas('roles', ['name' => 'banned']);
    }

    public function test_permissions_exist()
    {
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
        
        $this->assertDatabaseHas('permissions', ['name' => 'view_forums']);
        $this->assertDatabaseHas('permissions', ['name' => 'create_topics']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage_users']);
        $this->assertDatabaseHas('permissions', ['name' => 'access_admin_panel']);
        $this->assertDatabaseHas('permissions', ['name' => 'moderate_posts']);
    }

    public function test_role_permissions_are_assigned()
    {
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
        
        // Check that super admin has many permissions
        $this->assertDatabaseHas('role_permissions', [
            'role_id' => 1, // super_admin
            'permission_id' => 1 // view_forums
        ]);
        
        $this->assertDatabaseHas('role_permissions', [
            'role_id' => 1, // super_admin
            'permission_id' => 22 // manage_users
        ]);
        
        // Check that user role has basic permissions
        $this->assertDatabaseHas('role_permissions', [
            'role_id' => 4, // user
            'permission_id' => 1 // view_forums
        ]);
    }

    public function test_super_admin_has_role_assigned()
    {
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
        
        $this->assertDatabaseHas('user_roles', [
            'user_id' => 1, // super admin user
            'role_id' => 1  // super admin role
        ]);
    }
}
