<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Welcome to AI-phpBB4');
        $response->assertSee('Search forums, categories, or topics');
    }

    public function test_home_page_displays_community_stats(): void
    {
        // Create some test data
        User::factory()->count(5)->create();
        
        Livewire::test('home-page')
            ->assertSee('Community Members')
            ->assertSee('User Roles')
            ->assertSee('Permissions System');
    }

    public function test_search_functionality_works(): void
    {
        Livewire::test('home-page')
            ->set('searchQuery', 'general')
            ->assertSee('General Discussion')
            ->assertSet('showSearchResults', true);
    }

    public function test_clear_search_works(): void
    {
        Livewire::test('home-page')
            ->set('searchQuery', 'test')
            ->call('clearSearch')
            ->assertSet('searchQuery', '')
            ->assertSet('showSearchResults', false);
    }

    public function test_guest_sees_register_link(): void
    {
        $response = $this->get('/');
        
        $response->assertSee('Get started');
        $response->assertSee('Browse forums');
    }

    public function test_authenticated_user_sees_different_cta(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/');
        
        $response->assertSee('Browse Forums');
        $response->assertSee('Create a topic');
    }
}
