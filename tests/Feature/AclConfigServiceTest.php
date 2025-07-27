<?php

namespace Tests\Feature;

use App\Services\AclConfigService;
use Tests\TestCase;

class AclConfigServiceTest extends TestCase
{
    public function test_can_get_all_permissions()
    {
        $permissions = AclConfigService::getAllPermissions();
        
        $this->assertGreaterThan(30, $permissions->count());
        $this->assertTrue($permissions->contains('name', 'view_forums'));
        $this->assertTrue($permissions->contains('name', 'manage_forums'));
        $this->assertTrue($permissions->contains('name', 'access_admin_panel'));
    }

    public function test_can_get_permissions_by_category()
    {
        $forumPermissions = AclConfigService::getPermissionsByCategory('forum');
        
        $this->assertGreaterThan(0, $forumPermissions->count());
        $this->assertTrue($forumPermissions->contains('name', 'view_forums'));
        $this->assertTrue($forumPermissions->contains('name', 'manage_forums'));
    }

    public function test_can_get_all_roles()
    {
        $roles = AclConfigService::getAllRoles();
        
        $this->assertGreaterThan(4, $roles->count());
        $this->assertTrue($roles->contains('name', 'super_admin'));
        $this->assertTrue($roles->contains('name', 'user'));
    }

    public function test_can_get_role_by_name()
    {
        $superAdmin = AclConfigService::getRole('super_admin');
        
        $this->assertNotNull($superAdmin);
        $this->assertEquals('super_admin', $superAdmin['name']);
        $this->assertEquals('Super Admin', $superAdmin['display_name']);
        $this->assertEquals(1000, $superAdmin['level']);
    }

    public function test_can_get_role_permissions()
    {
        $superAdminPermissions = AclConfigService::getRolePermissions('super_admin');
        $userPermissions = AclConfigService::getRolePermissions('user');
        
        // Super admin should have all permissions
        $this->assertGreaterThan(30, $superAdminPermissions->count());
        
        // User should have limited permissions
        $this->assertGreaterThan(5, $userPermissions->count());
        $this->assertLessThan(15, $userPermissions->count());
    }

    public function test_can_check_role_has_permission()
    {
        $this->assertTrue(AclConfigService::roleHasPermission('super_admin', 'access_admin_panel'));
        $this->assertTrue(AclConfigService::roleHasPermission('user', 'view_forums'));
        $this->assertFalse(AclConfigService::roleHasPermission('user', 'access_admin_panel'));
    }

    public function test_can_get_default_role()
    {
        $defaultRole = AclConfigService::getDefaultRole();
        
        $this->assertNotNull($defaultRole);
        $this->assertEquals('user', $defaultRole['name']);
        $this->assertTrue($defaultRole['is_default']);
    }

    public function test_can_get_permission_categories()
    {
        $categories = AclConfigService::getPermissionCategories();
        
        $this->assertTrue($categories->contains('forum'));
        $this->assertTrue($categories->contains('topic'));
        $this->assertTrue($categories->contains('post'));
        $this->assertTrue($categories->contains('admin'));
    }

    public function test_can_get_role_hierarchy()
    {
        $hierarchy = AclConfigService::getRoleHierarchy();
        
        $this->assertEquals('super_admin', $hierarchy->first()['name']);
        $this->assertEquals('banned', $hierarchy->last()['name']);
    }

    public function test_config_validation()
    {
        $errors = AclConfigService::validateConfig();
        
        $this->assertEmpty($errors, 'Config validation should pass: ' . implode(', ', $errors));
    }
}
