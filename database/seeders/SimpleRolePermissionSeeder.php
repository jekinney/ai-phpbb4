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
            // Forum Management
            ['name' => 'view_forums', 'display_name' => 'View Forums', 'category' => 'forum', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_forums', 'display_name' => 'Manage Forums', 'category' => 'forum', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view_hidden_forums', 'display_name' => 'View Hidden Forums', 'category' => 'forum', 'created_at' => now(), 'updated_at' => now()],
            
            // Topic Management
            ['name' => 'view_topics', 'display_name' => 'View Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'create_topics', 'display_name' => 'Create Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'edit_own_topics', 'display_name' => 'Edit Own Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'edit_all_topics', 'display_name' => 'Edit All Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'delete_own_topics', 'display_name' => 'Delete Own Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'delete_all_topics', 'display_name' => 'Delete All Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'moderate_topics', 'display_name' => 'Moderate Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lock_topics', 'display_name' => 'Lock Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'sticky_topics', 'display_name' => 'Sticky Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'move_topics', 'display_name' => 'Move Topics', 'category' => 'topic', 'created_at' => now(), 'updated_at' => now()],
            
            // Post Management
            ['name' => 'view_posts', 'display_name' => 'View Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'create_posts', 'display_name' => 'Create Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'edit_own_posts', 'display_name' => 'Edit Own Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'edit_all_posts', 'display_name' => 'Edit All Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'delete_own_posts', 'display_name' => 'Delete Own Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'delete_all_posts', 'display_name' => 'Delete All Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'moderate_posts', 'display_name' => 'Moderate Posts', 'category' => 'post', 'created_at' => now(), 'updated_at' => now()],
            
            // User Management
            ['name' => 'view_users', 'display_name' => 'View Users', 'category' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'category' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ban_users', 'display_name' => 'Ban Users', 'category' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'edit_user_roles', 'display_name' => 'Edit User Roles', 'category' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view_user_details', 'display_name' => 'View User Details', 'category' => 'user', 'created_at' => now(), 'updated_at' => now()],
            
            // Moderation
            ['name' => 'access_mod_panel', 'display_name' => 'Access Mod Panel', 'category' => 'moderation', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'category' => 'moderation', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'handle_reports', 'display_name' => 'Handle Reports', 'category' => 'moderation', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view_mod_logs', 'display_name' => 'View Mod Logs', 'category' => 'moderation', 'created_at' => now(), 'updated_at' => now()],
            
            // Administration
            ['name' => 'access_admin_panel', 'display_name' => 'Access Admin Panel', 'category' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'category' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'category' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view_system_logs', 'display_name' => 'View System Logs', 'category' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'backup_restore', 'display_name' => 'Backup Restore', 'category' => 'admin', 'created_at' => now(), 'updated_at' => now()],
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
            // Super admin gets ALL permissions (1-34)
            ['role_id' => 1, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()], // view_forums
            ['role_id' => 1, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()], // manage_forums
            ['role_id' => 1, 'permission_id' => 3, 'created_at' => now(), 'updated_at' => now()], // view_hidden_forums
            ['role_id' => 1, 'permission_id' => 4, 'created_at' => now(), 'updated_at' => now()], // view_topics
            ['role_id' => 1, 'permission_id' => 5, 'created_at' => now(), 'updated_at' => now()], // create_topics
            ['role_id' => 1, 'permission_id' => 6, 'created_at' => now(), 'updated_at' => now()], // edit_own_topics
            ['role_id' => 1, 'permission_id' => 7, 'created_at' => now(), 'updated_at' => now()], // edit_all_topics
            ['role_id' => 1, 'permission_id' => 8, 'created_at' => now(), 'updated_at' => now()], // delete_own_topics
            ['role_id' => 1, 'permission_id' => 9, 'created_at' => now(), 'updated_at' => now()], // delete_all_topics
            ['role_id' => 1, 'permission_id' => 10, 'created_at' => now(), 'updated_at' => now()], // moderate_topics
            ['role_id' => 1, 'permission_id' => 11, 'created_at' => now(), 'updated_at' => now()], // lock_topics
            ['role_id' => 1, 'permission_id' => 12, 'created_at' => now(), 'updated_at' => now()], // sticky_topics
            ['role_id' => 1, 'permission_id' => 13, 'created_at' => now(), 'updated_at' => now()], // move_topics
            ['role_id' => 1, 'permission_id' => 14, 'created_at' => now(), 'updated_at' => now()], // view_posts
            ['role_id' => 1, 'permission_id' => 15, 'created_at' => now(), 'updated_at' => now()], // create_posts
            ['role_id' => 1, 'permission_id' => 16, 'created_at' => now(), 'updated_at' => now()], // edit_own_posts
            ['role_id' => 1, 'permission_id' => 17, 'created_at' => now(), 'updated_at' => now()], // edit_all_posts
            ['role_id' => 1, 'permission_id' => 18, 'created_at' => now(), 'updated_at' => now()], // delete_own_posts
            ['role_id' => 1, 'permission_id' => 19, 'created_at' => now(), 'updated_at' => now()], // delete_all_posts
            ['role_id' => 1, 'permission_id' => 20, 'created_at' => now(), 'updated_at' => now()], // moderate_posts
            ['role_id' => 1, 'permission_id' => 21, 'created_at' => now(), 'updated_at' => now()], // view_users
            ['role_id' => 1, 'permission_id' => 22, 'created_at' => now(), 'updated_at' => now()], // manage_users
            ['role_id' => 1, 'permission_id' => 23, 'created_at' => now(), 'updated_at' => now()], // ban_users
            ['role_id' => 1, 'permission_id' => 24, 'created_at' => now(), 'updated_at' => now()], // edit_user_roles
            ['role_id' => 1, 'permission_id' => 25, 'created_at' => now(), 'updated_at' => now()], // view_user_details
            ['role_id' => 1, 'permission_id' => 26, 'created_at' => now(), 'updated_at' => now()], // access_mod_panel
            ['role_id' => 1, 'permission_id' => 27, 'created_at' => now(), 'updated_at' => now()], // view_reports
            ['role_id' => 1, 'permission_id' => 28, 'created_at' => now(), 'updated_at' => now()], // handle_reports
            ['role_id' => 1, 'permission_id' => 29, 'created_at' => now(), 'updated_at' => now()], // view_mod_logs
            ['role_id' => 1, 'permission_id' => 30, 'created_at' => now(), 'updated_at' => now()], // access_admin_panel
            ['role_id' => 1, 'permission_id' => 31, 'created_at' => now(), 'updated_at' => now()], // manage_settings
            ['role_id' => 1, 'permission_id' => 32, 'created_at' => now(), 'updated_at' => now()], // manage_permissions
            ['role_id' => 1, 'permission_id' => 33, 'created_at' => now(), 'updated_at' => now()], // view_system_logs
            ['role_id' => 1, 'permission_id' => 34, 'created_at' => now(), 'updated_at' => now()], // backup_restore
            
            // Administrator gets most permissions (not super admin specific ones)
            ['role_id' => 2, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()], // view_forums
            ['role_id' => 2, 'permission_id' => 2, 'created_at' => now(), 'updated_at' => now()], // manage_forums
            ['role_id' => 2, 'permission_id' => 3, 'created_at' => now(), 'updated_at' => now()], // view_hidden_forums
            ['role_id' => 2, 'permission_id' => 4, 'created_at' => now(), 'updated_at' => now()], // view_topics
            ['role_id' => 2, 'permission_id' => 5, 'created_at' => now(), 'updated_at' => now()], // create_topics
            ['role_id' => 2, 'permission_id' => 6, 'created_at' => now(), 'updated_at' => now()], // edit_own_topics
            ['role_id' => 2, 'permission_id' => 7, 'created_at' => now(), 'updated_at' => now()], // edit_all_topics
            ['role_id' => 2, 'permission_id' => 8, 'created_at' => now(), 'updated_at' => now()], // delete_own_topics
            ['role_id' => 2, 'permission_id' => 9, 'created_at' => now(), 'updated_at' => now()], // delete_all_topics
            ['role_id' => 2, 'permission_id' => 10, 'created_at' => now(), 'updated_at' => now()], // moderate_topics
            ['role_id' => 2, 'permission_id' => 11, 'created_at' => now(), 'updated_at' => now()], // lock_topics
            ['role_id' => 2, 'permission_id' => 12, 'created_at' => now(), 'updated_at' => now()], // sticky_topics
            ['role_id' => 2, 'permission_id' => 13, 'created_at' => now(), 'updated_at' => now()], // move_topics
            ['role_id' => 2, 'permission_id' => 14, 'created_at' => now(), 'updated_at' => now()], // view_posts
            ['role_id' => 2, 'permission_id' => 15, 'created_at' => now(), 'updated_at' => now()], // create_posts
            ['role_id' => 2, 'permission_id' => 16, 'created_at' => now(), 'updated_at' => now()], // edit_own_posts
            ['role_id' => 2, 'permission_id' => 17, 'created_at' => now(), 'updated_at' => now()], // edit_all_posts
            ['role_id' => 2, 'permission_id' => 18, 'created_at' => now(), 'updated_at' => now()], // delete_own_posts
            ['role_id' => 2, 'permission_id' => 19, 'created_at' => now(), 'updated_at' => now()], // delete_all_posts
            ['role_id' => 2, 'permission_id' => 20, 'created_at' => now(), 'updated_at' => now()], // moderate_posts
            ['role_id' => 2, 'permission_id' => 21, 'created_at' => now(), 'updated_at' => now()], // view_users
            ['role_id' => 2, 'permission_id' => 22, 'created_at' => now(), 'updated_at' => now()], // manage_users
            ['role_id' => 2, 'permission_id' => 23, 'created_at' => now(), 'updated_at' => now()], // ban_users
            ['role_id' => 2, 'permission_id' => 24, 'created_at' => now(), 'updated_at' => now()], // edit_user_roles
            ['role_id' => 2, 'permission_id' => 25, 'created_at' => now(), 'updated_at' => now()], // view_user_details
            ['role_id' => 2, 'permission_id' => 26, 'created_at' => now(), 'updated_at' => now()], // access_mod_panel
            ['role_id' => 2, 'permission_id' => 27, 'created_at' => now(), 'updated_at' => now()], // view_reports
            ['role_id' => 2, 'permission_id' => 28, 'created_at' => now(), 'updated_at' => now()], // handle_reports
            ['role_id' => 2, 'permission_id' => 29, 'created_at' => now(), 'updated_at' => now()], // view_mod_logs
            ['role_id' => 2, 'permission_id' => 30, 'created_at' => now(), 'updated_at' => now()], // access_admin_panel
            
            // Moderator gets moderate permissions
            ['role_id' => 3, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()], // view_forums
            ['role_id' => 3, 'permission_id' => 4, 'created_at' => now(), 'updated_at' => now()], // view_topics
            ['role_id' => 3, 'permission_id' => 5, 'created_at' => now(), 'updated_at' => now()], // create_topics
            ['role_id' => 3, 'permission_id' => 6, 'created_at' => now(), 'updated_at' => now()], // edit_own_topics
            ['role_id' => 3, 'permission_id' => 8, 'created_at' => now(), 'updated_at' => now()], // delete_own_topics
            ['role_id' => 3, 'permission_id' => 10, 'created_at' => now(), 'updated_at' => now()], // moderate_topics
            ['role_id' => 3, 'permission_id' => 11, 'created_at' => now(), 'updated_at' => now()], // lock_topics
            ['role_id' => 3, 'permission_id' => 12, 'created_at' => now(), 'updated_at' => now()], // sticky_topics
            ['role_id' => 3, 'permission_id' => 13, 'created_at' => now(), 'updated_at' => now()], // move_topics
            ['role_id' => 3, 'permission_id' => 14, 'created_at' => now(), 'updated_at' => now()], // view_posts
            ['role_id' => 3, 'permission_id' => 15, 'created_at' => now(), 'updated_at' => now()], // create_posts
            ['role_id' => 3, 'permission_id' => 16, 'created_at' => now(), 'updated_at' => now()], // edit_own_posts
            ['role_id' => 3, 'permission_id' => 17, 'created_at' => now(), 'updated_at' => now()], // edit_all_posts
            ['role_id' => 3, 'permission_id' => 18, 'created_at' => now(), 'updated_at' => now()], // delete_own_posts
            ['role_id' => 3, 'permission_id' => 19, 'created_at' => now(), 'updated_at' => now()], // delete_all_posts
            ['role_id' => 3, 'permission_id' => 20, 'created_at' => now(), 'updated_at' => now()], // moderate_posts
            ['role_id' => 3, 'permission_id' => 21, 'created_at' => now(), 'updated_at' => now()], // view_users
            ['role_id' => 3, 'permission_id' => 26, 'created_at' => now(), 'updated_at' => now()], // access_mod_panel
            ['role_id' => 3, 'permission_id' => 27, 'created_at' => now(), 'updated_at' => now()], // view_reports
            ['role_id' => 3, 'permission_id' => 28, 'created_at' => now(), 'updated_at' => now()], // handle_reports
            ['role_id' => 3, 'permission_id' => 29, 'created_at' => now(), 'updated_at' => now()], // view_mod_logs
            
            // User gets basic permissions
            ['role_id' => 4, 'permission_id' => 1, 'created_at' => now(), 'updated_at' => now()], // view_forums
            ['role_id' => 4, 'permission_id' => 4, 'created_at' => now(), 'updated_at' => now()], // view_topics
            ['role_id' => 4, 'permission_id' => 5, 'created_at' => now(), 'updated_at' => now()], // create_topics
            ['role_id' => 4, 'permission_id' => 6, 'created_at' => now(), 'updated_at' => now()], // edit_own_topics
            ['role_id' => 4, 'permission_id' => 8, 'created_at' => now(), 'updated_at' => now()], // delete_own_topics
            ['role_id' => 4, 'permission_id' => 14, 'created_at' => now(), 'updated_at' => now()], // view_posts
            ['role_id' => 4, 'permission_id' => 15, 'created_at' => now(), 'updated_at' => now()], // create_posts
            ['role_id' => 4, 'permission_id' => 16, 'created_at' => now(), 'updated_at' => now()], // edit_own_posts
            ['role_id' => 4, 'permission_id' => 18, 'created_at' => now(), 'updated_at' => now()], // delete_own_posts
            ['role_id' => 4, 'permission_id' => 21, 'created_at' => now(), 'updated_at' => now()], // view_users
        ];
        DB::table('role_permissions')->insert($rolePermissions);
        
        // Create super admin user
        $userId = DB::table('users')->insertGetId([
            'name' => 'Super Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
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
