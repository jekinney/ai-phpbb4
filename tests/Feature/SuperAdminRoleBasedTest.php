<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminRoleBasedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
    }

    public function test_super_admin_works_purely_through_roles()
    {
        $superAdmin = User::where('email', 'admin@example.com')->first();
        
        // Verify super admin user doesn't have is_super_admin column/property
        $this->assertObjectNotHasProperty('is_super_admin', $superAdmin);
        
        // Verify super admin has the super_admin role
        $this->assertTrue($superAdmin->hasRole('super_admin'));
        
        // Verify super admin can do everything through role permissions
        $this->assertTrue($superAdmin->hasPermission('manage_forums'));
        $this->assertTrue($superAdmin->hasPermission('manage_users'));
        $this->assertTrue($superAdmin->hasPermission('access_admin_panel'));
        $this->assertTrue($superAdmin->hasPermission('backup_restore'));
        
        // Verify permission count matches expected (34 permissions)
        $permissionCount = $superAdmin->getAllPermissions()->count();
        $this->assertEquals(34, $permissionCount);
        
        // Verify super admin has exactly 1 role
        $this->assertEquals(1, $superAdmin->roles()->count());
        $this->assertEquals('super_admin', $superAdmin->roles()->first()->name);
    }

    public function test_regular_user_has_limited_permissions()
    {
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role
        
        // Verify regular user has basic permissions only
        $this->assertTrue($user->hasPermission('view_forums'));
        $this->assertTrue($user->hasPermission('create_posts'));
        
        // Verify regular user cannot do admin things
        $this->assertFalse($user->hasPermission('manage_forums'));
        $this->assertFalse($user->hasPermission('access_admin_panel'));
        
        // Verify permission count is limited (10 permissions for user role)
        $permissionCount = $user->getAllPermissions()->count();
        $this->assertEquals(10, $permissionCount);
    }
}
