<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertSee('Sign in to your account');
    }

    public function test_register_page_loads(): void
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertSee('Create your account');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        Livewire::test('login')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        Livewire::test('login')
            ->set('email', 'test@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_register(): void
    {
        Livewire::test('register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terms', true)
            ->call('register')
            ->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        Livewire::test('register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different-password')
            ->set('terms', true)
            ->call('register')
            ->assertHasErrors('password');

        $this->assertGuest();
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        Livewire::test('register')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('terms', false)
            ->call('register')
            ->assertHasErrors('terms');

        $this->assertGuest();
    }

    public function test_authenticated_users_cannot_access_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');
        
        $response->assertRedirect('/dashboard'); // Authenticated users redirect to dashboard
    }

    public function test_authenticated_users_cannot_access_register_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');
        
        $response->assertRedirect('/dashboard'); // Authenticated users redirect to dashboard
    }
}
