<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\HomePageContent;
use Tests\OptimizedTestDatabase;
use Tests\TestCase;

class HomePageManagerSimpleTest extends TestCase
{
    use OptimizedTestDatabase;

    public function test_home_page_manager_component_can_be_instantiated(): void
    {
        // Test that the Livewire component can be created
        $component = new \App\Livewire\Admin\HomePageManager();
        $this->assertInstanceOf(\App\Livewire\Admin\HomePageManager::class, $component);
    }
    
    public function test_home_page_content_model_works(): void
    {
        // Test that the HomePageContent model works
        $content = HomePageContent::create([
            'section' => 'hero',
            'key' => 'title',
            'title' => 'Welcome',
            'content' => 'Welcome to our site',
            'is_active' => true,
            'sort_order' => 0,
        ]);
        
        $this->assertInstanceOf(HomePageContent::class, $content);
        $this->assertEquals('hero', $content->section);
        $this->assertEquals('title', $content->key);
        $this->assertTrue($content->is_active);
    }

    public function test_home_page_manager_route_exists(): void
    {
        // Test that the route exists
        $response = $this->get('/admin/home-page');
        // We expect a redirect to login since we're not authenticated
        $response->assertRedirect();
    }
}
