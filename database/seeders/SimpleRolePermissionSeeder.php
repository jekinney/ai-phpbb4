<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SimpleRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('user_permissions')->truncate();
        DB::table('user_roles')->truncate();
        DB::table('role_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        
        // Insert permissions directly
        $permissions = [
            ['name' => 'view_forums', 'display_name' => 'View Forums', 'category' => 'forum', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'create_topics', 'display_name' => 'Create Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'create_posts', 'display_name' => 'Create Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'moderate_topics', 'display_name' => 'Moderate Topics', 'category' => 'moderation', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'category' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_forums', 'display_name' => 'Manage Forums', 'category' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '*', 'display_name' => 'Super Admin', 'category' => 'special', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('permissions')->insert($permissions);
        
        // Insert roles directly
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin', 'level' => 1000, 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'administrator', 'display_name' => 'Administrator', 'level' => 900, 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'moderator', 'display_name' => 'Moderator', 'level' => 500, 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'user', 'display_name' => 'User', 'level' => 100, 'is_default' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'banned', 'display_name' => 'Banned', 'level' => 0, 'is_default' => false, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('roles')->insert($roles);
        
        // Insert role permissions
        $rolePermissions = [
            // Super admin gets wildcard
            ['role_id' => 1, 'permission_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            // Admin gets most permissions
            ['role_id' => 2, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'permission_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'permission_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'permission_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'permission_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            // Moderator gets moderate permissions
            ['role_id' => 3, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'permission_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'permission_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            // User gets basic permissions
            ['role_id' => 4, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 4, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 4, 'permission_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('role_permissions')->insert($rolePermissions);
        
        // Create super admin user
        $userId = DB::table('users')->insertGetId([
            'name' => 'Super Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Assign super admin role
        DB::table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
