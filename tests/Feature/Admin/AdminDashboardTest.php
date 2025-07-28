<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Tests\OptimizedTestDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use OptimizedTestDatabase;

    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin');
        
        $response->assertRedirect('/login');
    }

    public function test_admin_dashboard_requires_permission(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admin');
        
        // Should get a 403 forbidden response if user doesn't have admin permission
        $response->assertStatus(403);
    }

    public function test_admin_dashboard_works_for_authorized_user(): void
    {
        // Create just the permission and role we need
        $permission = Permission::create(['name' => 'access_admin_panel', 'display_name' => 'Access Admin Panel']);
        $role = Role::create(['name' => 'Administrator', 'display_name' => 'Administrator']);
        $role->permissions()->attach($permission->id);
        
        $user = User::factory()->create();
        $user->assignRole($role);
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('stats');
    }

    public function test_admin_dashboard_displays_statistics(): void
    {
        // Create just the permission and role we need
        $permission = Permission::create(['name' => 'access_admin_panel', 'display_name' => 'Access Admin Panel']);
        $role = Role::create(['name' => 'Administrator', 'display_name' => 'Administrator']);
        $role->permissions()->attach($permission->id);
        
        $user = User::factory()->create();
        $user->assignRole($role);
        
        $response = $this->actingAs($user)->get('/admin');
        
        $response->assertOk();
        
        // Check that stats array exists and has expected keys
        $stats = $response->viewData('stats');
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_users', $stats);
        $this->assertArrayHasKey('total_roles', $stats);
        $this->assertArrayHasKey('total_permissions', $stats);
        $this->assertArrayHasKey('total_forums', $stats);
        
        // Verify basic counts
        $this->assertEquals(1, $stats['total_users']);
        $this->assertEquals(1, $stats['total_roles']);
        $this->assertEquals(1, $stats['total_permissions']);
        $this->assertEquals(0, $stats['total_forums']); // No forums yet
    }
}
