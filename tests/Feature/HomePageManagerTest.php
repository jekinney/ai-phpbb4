<?php

namespace Tests\Feature;

use App\Models\HomePageContent;
use App\Models\User;
use Tests\OptimizedTestDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomePageManagerTest extends TestCase
{
    use OptimizedTestDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the ACL system
        $this->seed('ConfigBasedRolePermissionSeeder');
        $this->seed('HomePageContentSeeder');
    }

    public function test_admin_can_access_home_page_manager(): void
    {
        // Create roles and permissions manually
        $role = \App\Models\Role::create([
            'name' => 'Super Administrator',
            'display_name' => 'Super Administrator',
            'description' => 'Has full access to all features',
            'level' => 100,
        ]);
        
        $permissions = [
            'access_admin_panel',
            'manage_home_page',
        ];
        
        foreach ($permissions as $permissionName) {
            $permission = \App\Models\Permission::create([
                'name' => $permissionName,
                'description' => 'Permission for ' . $permissionName,
                'category' => 'admin',
            ]);
            
            \App\Models\RolePermission::create([
                'role_id' => $role->id,
                'permission_id' => $permission->id,
            ]);
        }
        
        // Create a Super Administrator user
        $admin = User::factory()->create();
        $admin->assignRole('Super Administrator');
        
        // Verify the user has the correct permissions
        $this->assertTrue($admin->hasPermission('access_admin_panel'));
        $this->assertTrue($admin->hasPermission('manage_home_page'));
        
        // Test the actual route
        $response = $this->actingAs($admin)->get('/admin/home-page');

        $response->assertStatus(200);
        $response->assertSee('Home Page Manager');
    }

    public function test_regular_user_cannot_access_home_page_manager(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/admin/home-page');
        
        $response->assertStatus(403);
    }

    public function test_admin_can_view_content_list(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\HomePageManager::class)
            ->assertSee('Hero Title')
            ->assertSee('Welcome to AI-phpBB4')
            ->assertSee('hero')
            ->assertSee('title');
    }

    public function test_admin_can_edit_content(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $content = HomePageContent::first();

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\HomePageManager::class)
            ->call('editItem', $content->id)
            ->assertSet('showEditModal', true)
            ->assertSet('editingItem.id', $content->id)
            ->set('title', 'Updated Title')
            ->set('content', 'Updated Content')
            ->call('save')
            ->assertSet('showEditModal', false)
            ->assertSessionHas('message');

        $this->assertDatabaseHas('home_page_contents', [
            'id' => $content->id,
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);
    }

    public function test_admin_can_create_new_content(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\HomePageManager::class)
            ->call('createNew')
            ->assertSet('showEditModal', true)
            ->set('section', 'test')
            ->set('key', 'test_key')
            ->set('title', 'Test Title')
            ->set('content', 'Test Content')
            ->call('save')
            ->assertSet('showEditModal', false)
            ->assertSessionHas('message');

        $this->assertDatabaseHas('home_page_contents', [
            'section' => 'test',
            'key' => 'test_key',
            'title' => 'Test Title',
            'content' => 'Test Content',
        ]);
    }

    public function test_admin_can_toggle_content_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $content = HomePageContent::first();
        $originalStatus = $content->is_active;

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\HomePageManager::class)
            ->call('toggleStatus', $content->id)
            ->assertSessionHas('message');

        $this->assertDatabaseHas('home_page_contents', [
            'id' => $content->id,
            'is_active' => !$originalStatus,
        ]);
    }

    public function test_admin_can_delete_content(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $content = HomePageContent::create([
            'section' => 'test',
            'key' => 'deletable',
            'title' => 'Test',
            'content' => 'Test content',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\HomePageManager::class)
            ->call('delete', $content->id)
            ->assertSessionHas('message');

        $this->assertDatabaseMissing('home_page_contents', [
            'id' => $content->id,
        ]);
    }

    public function test_content_filtering_by_section_works(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\HomePageManager::class)
            ->set('selectedSection', 'hero')
            ->assertSee('title')
            ->assertSee('subtitle')
            ->set('selectedSection', 'news')
            ->assertSee('news_1')
            ->assertSee('news_2');
    }
}
