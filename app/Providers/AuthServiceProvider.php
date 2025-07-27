<?php

namespace App\Providers;

use App\Models\Forum;
use App\Models\Topic;
use App\Models\Post;
use App\Models\User;
use App\Policies\ForumPolicy;
use App\Policies\TopicPolicy;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Forum::class => ForumPolicy::class,
        Topic::class => TopicPolicy::class,
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // Register all permissions as Gates
        $permissions = collect([
            // Admin Panel Access
            'access_admin_panel',
            
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'ban_users',
            'unban_users',
            'manage_user_roles',
            'view_user_profiles',
            'impersonate_users',
            
            // Role & Permission Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'assign_roles',
            'view_permissions',
            'manage_permissions',
            
            // Forum Management
            'view_forums',
            'create_forums',
            'edit_forums',
            'delete_forums',
            'manage_forum_structure',
            'moderate_forums',
            
            // Content Management
            'create_topics',
            'edit_topics',
            'delete_topics',
            'moderate_topics',
            'create_posts',
            'edit_posts',
            'delete_posts',
            'moderate_posts',
            'manage_home_page',
            
            // System Management
            'view_system_logs',
            'manage_system_settings',
            'backup_database',
            'restore_database',
        ]);
        
        $permissions->each(function ($permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                return $user->hasPermission($permission);
            });
        });
    }
}
