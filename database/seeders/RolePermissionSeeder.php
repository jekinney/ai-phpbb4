<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define a constant to allow permission creation
        define('SEEDING_PERMISSIONS', true);

        // Create permissions by category
        $this->createPermissions();
        
        // Create roles
        $this->createRoles();
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles();
        
        // Create super admin user
        $this->createSuperAdmin();
    }

    private function createPermissions(): void
    {
        $permissions = [
            // Forum Management
            'forum' => [
                ['name' => 'view_forums', 'display_name' => 'View Forums', 'description' => 'Can view forum listings'],
                ['name' => 'manage_forums', 'display_name' => 'Manage Forums', 'description' => 'Can create, edit, delete forums and categories'],
                ['name' => 'view_hidden_forums', 'display_name' => 'View Hidden Forums', 'description' => 'Can view forums marked as hidden'],
            ],
            
            // Topic Management
            'topic' => [
                ['name' => 'view_topics', 'display_name' => 'View Topics', 'description' => 'Can view topic listings'],
                ['name' => 'create_topics', 'display_name' => 'Create Topics', 'description' => 'Can create new topics'],
                ['name' => 'edit_own_topics', 'display_name' => 'Edit Own Topics', 'description' => 'Can edit own topics'],
                ['name' => 'edit_all_topics', 'display_name' => 'Edit All Topics', 'description' => 'Can edit any topic'],
                ['name' => 'delete_own_topics', 'display_name' => 'Delete Own Topics', 'description' => 'Can delete own topics'],
                ['name' => 'delete_all_topics', 'display_name' => 'Delete All Topics', 'description' => 'Can delete any topic'],
                ['name' => 'moderate_topics', 'display_name' => 'Moderate Topics', 'description' => 'Can moderate topics (lock, sticky, move)'],
                ['name' => 'lock_topics', 'display_name' => 'Lock Topics', 'description' => 'Can lock/unlock topics'],
                ['name' => 'sticky_topics', 'display_name' => 'Sticky Topics', 'description' => 'Can make topics sticky'],
                ['name' => 'move_topics', 'display_name' => 'Move Topics', 'description' => 'Can move topics between forums'],
            ],
            
            // Post Management
            'post' => [
                ['name' => 'view_posts', 'display_name' => 'View Posts', 'description' => 'Can view posts'],
                ['name' => 'create_posts', 'display_name' => 'Create Posts', 'description' => 'Can create new posts'],
                ['name' => 'edit_own_posts', 'display_name' => 'Edit Own Posts', 'description' => 'Can edit own posts'],
                ['name' => 'edit_all_posts', 'display_name' => 'Edit All Posts', 'description' => 'Can edit any post'],
                ['name' => 'delete_own_posts', 'display_name' => 'Delete Own Posts', 'description' => 'Can delete own posts'],
                ['name' => 'delete_all_posts', 'display_name' => 'Delete All Posts', 'description' => 'Can delete any post'],
                ['name' => 'moderate_posts', 'display_name' => 'Moderate Posts', 'description' => 'Can moderate posts'],
            ],
            
            // User Management
            'user' => [
                ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'Can view user profiles'],
                ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Can create, edit, delete users'],
                ['name' => 'ban_users', 'display_name' => 'Ban Users', 'description' => 'Can ban/unban users'],
                ['name' => 'edit_user_roles', 'display_name' => 'Edit User Roles', 'description' => 'Can assign/remove roles from users'],
                ['name' => 'view_user_details', 'display_name' => 'View User Details', 'description' => 'Can view detailed user information'],
            ],
            
            // Moderation
            'moderation' => [
                ['name' => 'access_mod_panel', 'display_name' => 'Access Moderation Panel', 'description' => 'Can access moderation tools'],
                ['name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'Can view user reports'],
                ['name' => 'handle_reports', 'display_name' => 'Handle Reports', 'description' => 'Can resolve user reports'],
                ['name' => 'view_mod_logs', 'display_name' => 'View Moderation Logs', 'description' => 'Can view moderation activity logs'],
            ],
            
            // Administration
            'admin' => [
                ['name' => 'access_admin_panel', 'display_name' => 'Access Admin Panel', 'description' => 'Can access administration panel'],
                ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'description' => 'Can modify system settings'],
                ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'description' => 'Can manage roles and permissions'],
                ['name' => 'view_system_logs', 'display_name' => 'View System Logs', 'description' => 'Can view system logs'],
                ['name' => 'backup_restore', 'display_name' => 'Backup & Restore', 'description' => 'Can backup and restore system'],
            ],
            
            // Special
            'special' => [
                ['name' => '*', 'display_name' => 'Super Admin', 'description' => 'Can perform any action (super admin only)'],
            ],
        ];

        foreach ($permissions as $category => $categoryPermissions) {
            foreach ($categoryPermissions as $permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description'],
                    'category' => $category,
                ]);
            }
        }
    }

    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Has all permissions and cannot be restricted',
                'level' => 1000,
                'is_default' => false,
            ],
            [
                'name' => 'administrator',
                'display_name' => 'Administrator',
                'description' => 'Can manage forums, users, and most system functions',
                'level' => 900,
                'is_default' => false,
            ],
            [
                'name' => 'global_moderator',
                'display_name' => 'Global Moderator',
                'description' => 'Can moderate all forums and manage users',
                'level' => 700,
                'is_default' => false,
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Moderator',
                'description' => 'Can moderate assigned forums',
                'level' => 500,
                'is_default' => false,
            ],
            [
                'name' => 'trusted_user',
                'display_name' => 'Trusted User',
                'description' => 'Regular user with some additional privileges',
                'level' => 200,
                'is_default' => false,
            ],
            [
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Regular forum user',
                'level' => 100,
                'is_default' => true,
            ],
            [
                'name' => 'guest',
                'display_name' => 'Guest',
                'description' => 'Non-registered user (read-only)',
                'level' => 50,
                'is_default' => false,
            ],
            [
                'name' => 'banned',
                'display_name' => 'Banned',
                'description' => 'User is banned from the forum',
                'level' => 0,
                'is_default' => false,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }

    private function assignPermissionsToRoles(): void
    {
        // Super Admin - gets the wildcard permission
        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdmin->givePermissionTo('*');

        // Administrator - gets most permissions except super admin wildcard
        $admin = Role::where('name', 'administrator')->first();
        $adminPermissions = [
            'view_forums', 'manage_forums', 'view_hidden_forums',
            'view_topics', 'create_topics', 'edit_all_topics', 'delete_all_topics', 
            'moderate_topics', 'lock_topics', 'sticky_topics', 'move_topics',
            'view_posts', 'create_posts', 'edit_all_posts', 'delete_all_posts', 'moderate_posts',
            'view_users', 'manage_users', 'ban_users', 'edit_user_roles', 'view_user_details',
            'access_mod_panel', 'view_reports', 'handle_reports', 'view_mod_logs',
            'access_admin_panel', 'manage_settings', 'manage_permissions', 'view_system_logs', 'backup_restore'
        ];
        foreach ($adminPermissions as $permission) {
            $admin->givePermissionTo($permission);
        }

        // Global Moderator
        $globalMod = Role::where('name', 'global_moderator')->first();
        $globalModPermissions = [
            'view_forums', 'view_hidden_forums',
            'view_topics', 'create_topics', 'edit_all_topics', 'delete_all_topics',
            'moderate_topics', 'lock_topics', 'sticky_topics', 'move_topics',
            'view_posts', 'create_posts', 'edit_all_posts', 'delete_all_posts', 'moderate_posts',
            'view_users', 'ban_users', 'view_user_details',
            'access_mod_panel', 'view_reports', 'handle_reports', 'view_mod_logs'
        ];
        foreach ($globalModPermissions as $permission) {
            $globalMod->givePermissionTo($permission);
        }

        // Moderator
        $moderator = Role::where('name', 'moderator')->first();
        $moderatorPermissions = [
            'view_forums',
            'view_topics', 'create_topics', 'edit_own_topics', 
            'moderate_topics', 'lock_topics', 'sticky_topics',
            'view_posts', 'create_posts', 'edit_own_posts', 'moderate_posts',
            'view_users', 'view_user_details',
            'access_mod_panel', 'view_reports', 'handle_reports'
        ];
        foreach ($moderatorPermissions as $permission) {
            $moderator->givePermissionTo($permission);
        }

        // Trusted User
        $trustedUser = Role::where('name', 'trusted_user')->first();
        $trustedUserPermissions = [
            'view_forums',
            'view_topics', 'create_topics', 'edit_own_topics', 'delete_own_topics',
            'view_posts', 'create_posts', 'edit_own_posts', 'delete_own_posts',
            'view_users'
        ];
        foreach ($trustedUserPermissions as $permission) {
            $trustedUser->givePermissionTo($permission);
        }

        // Regular User
        $user = Role::where('name', 'user')->first();
        $userPermissions = [
            'view_forums',
            'view_topics', 'create_topics', 'edit_own_topics',
            'view_posts', 'create_posts', 'edit_own_posts',
            'view_users'
        ];
        foreach ($userPermissions as $permission) {
            $user->givePermissionTo($permission);
        }

        // Guest
        $guest = Role::where('name', 'guest')->first();
        $guestPermissions = [
            'view_forums',
            'view_topics',
            'view_posts'
        ];
        foreach ($guestPermissions as $permission) {
            $guest->givePermissionTo($permission);
        }

        // Banned - gets no permissions (empty by default)
    }

    private function createSuperAdmin(): void
    {
        // Create super admin user without triggering the booted event
        $superAdmin = new User([
            'name' => 'Super Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->saveQuietly(); // Saves without firing events

        // Assign super admin role directly
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $superAdmin->roles()->attach($superAdminRole->id);

        // Create a few test users with different roles
        $testUsers = [
            [
                'name' => 'Test Administrator',
                'email' => 'test.admin@example.com',
                'role' => 'administrator'
            ],
            [
                'name' => 'Test Moderator',
                'email' => 'test.mod@example.com',
                'role' => 'moderator'
            ],
            [
                'name' => 'Test User',
                'email' => 'test.user@example.com',
                'role' => 'user'
            ]
        ];

        foreach ($testUsers as $userData) {
            $user = new User([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $user->saveQuietly();

            $role = Role::where('name', $userData['role'])->first();
            $user->roles()->attach($role->id);
        }
    }
}
