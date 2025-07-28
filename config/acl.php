<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ACL Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all permissions organized by categories.
    | These permissions are seeded into the database and used by the ACL system.
    |
    */

    'permissions' => [
        // Forum Management
        'forum' => [
            'view_forums' => 'View Forums',
            'manage_forums' => 'Manage Forums',
            'view_hidden_forums' => 'View Hidden Forums',
        ],

        // Topic Management
        'topic' => [
            'view_topics' => 'View Topics',
            'create_topics' => 'Create Topics',
            'edit_own_topics' => 'Edit Own Topics',
            'edit_all_topics' => 'Edit All Topics',
            'delete_own_topics' => 'Delete Own Topics',
            'delete_all_topics' => 'Delete All Topics',
            'moderate_topics' => 'Moderate Topics',
            'lock_topics' => 'Lock Topics',
            'sticky_topics' => 'Sticky Topics',
            'move_topics' => 'Move Topics',
            'follow_topics' => 'Follow Topics',
        ],

        // Post Management
        'post' => [
            'view_posts' => 'View Posts',
            'create_posts' => 'Create Posts',
            'edit_own_posts' => 'Edit Own Posts',
            'edit_all_posts' => 'Edit All Posts',
            'delete_own_posts' => 'Delete Own Posts',
            'delete_all_posts' => 'Delete All Posts',
            'moderate_posts' => 'Moderate Posts',
        ],

        // User Management
        'user' => [
            'view_users' => 'View Users',
            'manage_users' => 'Manage Users',
            'ban_users' => 'Ban Users',
            'edit_user_roles' => 'Edit User Roles',
            'view_user_details' => 'View User Details',
        ],

        // Moderation
        'moderation' => [
            'access_mod_panel' => 'Access Mod Panel',
            'view_reports' => 'View Reports',
            'handle_reports' => 'Handle Reports',
            'view_mod_logs' => 'View Mod Logs',
        ],

        // Administration
        'admin' => [
            'access_admin_panel' => 'Access Admin Panel',
            'manage_settings' => 'Manage Settings',
            'manage_permissions' => 'Manage Permissions',
            'view_system_logs' => 'View System Logs',
            'backup_restore' => 'Backup Restore',
            'view_documentation' => 'View Documentation',
            'manage_static_pages' => 'Manage Static Pages',
        ],

        // File Management
        'files' => [
            'upload_files' => 'Upload Files',
            'manage_attachments' => 'Manage Attachments',
            'delete_any_attachment' => 'Delete Any Attachment',
            'view_file_stats' => 'View File Statistics',
            'manage_file_settings' => 'Manage File Settings',
        ],

        // Personal Messages
        'messages' => [
            'send_messages' => 'Send Personal Messages',
            'receive_messages' => 'Receive Personal Messages',
            'delete_own_messages' => 'Delete Own Messages',
            'delete_any_message' => 'Delete Any Message (Admin)',
            'view_all_messages' => 'View All Messages (Admin)',
            'bypass_message_limit' => 'Bypass Message Limits',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ACL Roles Configuration
    |--------------------------------------------------------------------------
    |
    | This section defines all roles and their permission assignments.
    | Higher level numbers indicate higher privileges.
    |
    */

    'roles' => [
        'super_admin' => [
            'display_name' => 'Super Admin',
            'level' => 1000,
            'is_default' => false,
            'permissions' => 'all', // Special case: gets all permissions
        ],

        'administrator' => [
            'display_name' => 'Administrator',
            'level' => 900,
            'is_default' => false,
            'permissions' => [
                // Forum permissions
                'view_forums',
                'manage_forums',
                'view_hidden_forums',
                
                // Topic permissions
                'view_topics',
                'create_topics',
                'edit_own_topics',
                'edit_all_topics',
                'delete_own_topics',
                'delete_all_topics',
                'moderate_topics',
                'lock_topics',
                'sticky_topics',
                'move_topics',
                'follow_topics',
                
                // Post permissions
                'view_posts',
                'create_posts',
                'edit_own_posts',
                'edit_all_posts',
                'delete_own_posts',
                'delete_all_posts',
                'moderate_posts',
                
                // User permissions
                'view_users',
                'manage_users',
                'ban_users',
                'edit_user_roles',
                'view_user_details',
                
                // Moderation permissions
                'access_mod_panel',
                'view_reports',
                'handle_reports',
                'view_mod_logs',
                
                // Admin permissions
                'access_admin_panel',
                'manage_settings',
                'manage_permissions',
                'view_system_logs',
                'backup_restore',
                'view_documentation',
                'manage_static_pages',
                
                // File permissions
                'upload_files',
                'manage_attachments',
                'delete_any_attachment',
                'view_file_stats',
                'manage_file_settings',
            ],
        ],

        'moderator' => [
            'display_name' => 'Moderator',
            'level' => 500,
            'is_default' => false,
            'permissions' => [
                'view_forums',
                'view_topics',
                'create_topics',
                'edit_own_topics',
                'delete_own_topics',
                'moderate_topics',
                'lock_topics',
                'sticky_topics',
                'move_topics',
                'follow_topics',
                'view_posts',
                'create_posts',
                'edit_own_posts',
                'edit_all_posts',
                'delete_own_posts',
                'delete_all_posts',
                'moderate_posts',
                'view_users',
                'access_mod_panel',
                'view_reports',
                'handle_reports',
                'view_mod_logs',
                'send_messages',
                'receive_messages',
                'delete_own_messages',
                'delete_any_message',
            ],
        ],

        'user' => [
            'display_name' => 'User',
            'level' => 100,
            'is_default' => true,
            'permissions' => [
                'view_forums',
                'view_topics',
                'create_topics',
                'edit_own_topics',
                'delete_own_topics',
                'follow_topics',
                'view_posts',
                'create_posts',
                'edit_own_posts',
                'delete_own_posts',
                'view_users',
                'upload_files',
                'send_messages',
                'receive_messages',
                'delete_own_messages',
            ],
        ],

        'banned' => [
            'display_name' => 'Banned',
            'level' => 0,
            'is_default' => false,
            'permissions' => [], // No permissions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Super Admin Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the default super admin user created during seeding.
    |
    */

    'super_admin_user' => [
        'name' => 'Super Administrator',
        'email' => 'admin@example.com',
        'password' => 'password', // Will be hashed automatically
    ],
];
