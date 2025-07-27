<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRouteTest extends TestCase
{
    public function test_admin_routes_are_registered(): void
    {
        // Test that admin routes exist
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->map(fn($route) => $route->getName())
            ->filter(fn($name) => str_starts_with($name ?? '', 'admin.'));

        $expectedRoutes = [
            'admin.dashboard',
            'admin.users.index',
            'admin.users.show',
            'admin.roles.index',
            'admin.permissions.index',
            'admin.forums.index',
            'admin.settings.index',
            'admin.logs.index',
        ];

        foreach ($expectedRoutes as $route) {
            $this->assertTrue($routes->contains($route), "Route {$route} is not registered");
        }
    }

    public function test_admin_dashboard_redirect_for_guest(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }
}
