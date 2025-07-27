<?php

namespace App\Services;

use Illuminate\Support\Collection;

class AclConfigService
{
    /**
     * Get all permissions from config
     */
    public static function getAllPermissions(): Collection
    {
        $permissions = collect();
        $permissionsConfig = config('acl.permissions');

        foreach ($permissionsConfig as $category => $categoryPermissions) {
            foreach ($categoryPermissions as $name => $displayName) {
                $permissions->push([
                    'name' => $name,
                    'display_name' => $displayName,
                    'category' => $category,
                ]);
            }
        }

        return $permissions;
    }

    /**
     * Get permissions for a specific category
     */
    public static function getPermissionsByCategory(string $category): Collection
    {
        $categoryPermissions = config("acl.permissions.{$category}", []);
        
        return collect($categoryPermissions)->map(function ($displayName, $name) use ($category) {
            return [
                'name' => $name,
                'display_name' => $displayName,
                'category' => $category,
            ];
        })->values();
    }

    /**
     * Get all roles from config
     */
    public static function getAllRoles(): Collection
    {
        $rolesConfig = config('acl.roles');
        
        return collect($rolesConfig)->map(function ($roleData, $name) {
            return array_merge(['name' => $name], $roleData);
        })->values();
    }

    /**
     * Get role by name
     */
    public static function getRole(string $name): ?array
    {
        $roleData = config("acl.roles.{$name}");
        
        if (!$roleData) {
            return null;
        }
        
        return array_merge(['name' => $name], $roleData);
    }

    /**
     * Get permissions for a specific role
     */
    public static function getRolePermissions(string $roleName): Collection
    {
        $roleData = self::getRole($roleName);
        
        if (!$roleData) {
            return collect();
        }

        $permissions = $roleData['permissions'];

        if ($permissions === 'all') {
            return self::getAllPermissions()->pluck('name');
        }

        return collect($permissions);
    }

    /**
     * Check if a role has a specific permission
     */
    public static function roleHasPermission(string $roleName, string $permission): bool
    {
        $rolePermissions = self::getRolePermissions($roleName);
        
        return $rolePermissions->contains($permission);
    }

    /**
     * Get super admin configuration
     */
    public static function getSuperAdminConfig(): array
    {
        return config('acl.super_admin_user');
    }

    /**
     * Get default role
     */
    public static function getDefaultRole(): ?array
    {
        $roles = self::getAllRoles();
        
        return $roles->firstWhere('is_default', true);
    }

    /**
     * Get all permission categories
     */
    public static function getPermissionCategories(): Collection
    {
        return collect(array_keys(config('acl.permissions')));
    }

    /**
     * Get role hierarchy (sorted by level)
     */
    public static function getRoleHierarchy(): Collection
    {
        return self::getAllRoles()->sortByDesc('level');
    }

    /**
     * Check if config is valid
     */
    public static function validateConfig(): array
    {
        $errors = [];

        // Check if config exists
        if (!config('acl')) {
            $errors[] = 'ACL config file not found';
            return $errors;
        }

        // Check permissions structure
        $permissions = config('acl.permissions');
        if (!is_array($permissions) || empty($permissions)) {
            $errors[] = 'Permissions configuration is invalid or empty';
        }

        // Check roles structure
        $roles = config('acl.roles');
        if (!is_array($roles) || empty($roles)) {
            $errors[] = 'Roles configuration is invalid or empty';
        }

        // Check if there's a default role
        $hasDefault = collect($roles)->contains('is_default', true);
        if (!$hasDefault) {
            $errors[] = 'No default role defined';
        }

        // Check super admin config
        $superAdmin = config('acl.super_admin_user');
        if (!is_array($superAdmin) || !isset($superAdmin['email'])) {
            $errors[] = 'Super admin configuration is invalid';
        }

        return $errors;
    }
}
