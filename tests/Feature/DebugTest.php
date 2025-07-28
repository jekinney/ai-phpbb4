<?php

namespace Tests\Feature;

use App\Models\Forum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'SimpleRolePermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'ForumSeeder']);
    }

    public function test_debug_permissions()
    {
        // Check if admin exists
        $admin = User::where('email', 'admin@example.com')->first();
        echo "\nAdmin exists: " . ($admin ? 'Yes' : 'No') . "\n";
        
        // Create regular user
        $user = User::factory()->create();
        $user->roles()->attach(4); // user role
        
        // Refresh the user to load relationships
        $user = $user->fresh();
        
        // Check if user has the role
        echo "User roles: " . $user->roles()->pluck('name')->join(', ') . "\n";
        
        // Check permissions
        echo "User has view_forums permission: " . ($user->hasPermission('view_forums') ? 'Yes' : 'No') . "\n";
        
        // Check via can method
        echo "User can view_forums: " . ($user->can('view_forums') ? 'Yes' : 'No') . "\n";
        
        // Test with forum model
        $forum = Forum::first();
        if ($forum) {
            echo "Forum is_hidden: " . ($forum->is_hidden ?? 'null') . "\n";
            
            // Test policy directly
            $policy = new \App\Policies\ForumPolicy();
            $policyResult = $policy->view($user, $forum);
            echo "Policy view result: " . ($policyResult ? 'Yes' : 'No') . "\n";
            
            // Test via Gate facade
            $gateResult = \Illuminate\Support\Facades\Gate::forUser($user)->allows('view', $forum);
            echo "Gate facade result: " . ($gateResult ? 'Yes' : 'No') . "\n";
            
            echo "User can view forum via policy: " . ($user->can('view', $forum) ? 'Yes' : 'No') . "\n";
        } else {
            echo "No forum found\n";
        }
        
        $this->assertTrue(true); // Always pass
    }
}
