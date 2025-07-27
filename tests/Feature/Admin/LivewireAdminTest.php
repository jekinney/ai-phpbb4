<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class LivewireAdminTest extends TestCase
{
    public function test_livewire_admin_routes_are_registered(): void
    {
        // Test that Livewire admin routes exist
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->map(fn($route) => [
                'name' => $route->getName(),
                'action' => $route->getActionName()
            ])
            ->filter(fn($route) => str_starts_with($route['name'] ?? '', 'admin.'));

        // Check dashboard uses Livewire
        $dashboardRoute = $routes->firstWhere('name', 'admin.dashboard');
        $this->assertStringContainsString('App\Livewire\Admin\Dashboard', $dashboardRoute['action']);

        // Check users index uses Livewire
        $usersRoute = $routes->firstWhere('name', 'admin.users.index');
        $this->assertStringContainsString('App\Livewire\Admin\UserIndex', $usersRoute['action']);

        // Check roles index uses Livewire
        $rolesRoute = $routes->firstWhere('name', 'admin.roles.index');
        $this->assertStringContainsString('App\Livewire\Admin\RoleIndex', $rolesRoute['action']);
    }

    public function test_admin_dashboard_redirect_for_guest(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }
}
