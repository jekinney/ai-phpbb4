<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PureLivewireTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations if needed
        $this->artisan('migrate');
    }

    /** @test */
    public function home_page_pure_livewire_renders()
    {
        // Create a test user to authenticate
        $user = User::factory()->create();
        
        // Act as the authenticated user and visit the home page
        $response = $this->actingAs($user)->get('/');
        
        // Assert the page loads successfully
        $response->assertStatus(200);
        
        // Assert that the HomePage Livewire component is present
        $response->assertSeeLivewire('home-page');
    }

    /** @test */
    public function admin_home_page_manager_pure_livewire_structure()
    {
        // Create an admin user
        $admin = User::factory()->create();
        
        // Visit the admin home page manager
        $response = $this->actingAs($admin)->get('/admin/home-page-manager');
        
        // Assert the page loads successfully
        $response->assertStatus(200);
        
        // Assert that the HomePageManager Livewire component is present
        $response->assertSeeLivewire('home-page-manager');
    }

    /** @test */
    public function livewire_components_dont_use_blade_views()
    {
        // Check HomePage component
        $homePageReflection = new \ReflectionClass(\App\Livewire\HomePage::class);
        $homePageFile = $homePageReflection->getFileName();
        $homePageContent = file_get_contents($homePageFile);
        
        // Assert no blade view references
        $this->assertStringNotContainsString('view(', $homePageContent);
        $this->assertStringNotContainsString('@extends', $homePageContent);
        $this->assertStringNotContainsString('@include', $homePageContent);
        
        // Check HomePageManager component
        $managerReflection = new \ReflectionClass(\App\Livewire\HomePageManager::class);
        $managerFile = $managerReflection->getFileName();
        $managerContent = file_get_contents($managerFile);
        
        // Assert no blade view references
        $this->assertStringNotContainsString('view(', $managerContent);
        $this->assertStringNotContainsString('@extends', $managerContent);
        $this->assertStringNotContainsString('@include', $managerContent);
    }
}
