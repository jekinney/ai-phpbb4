<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed ACL system first (roles, permissions, super admin)
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Create additional test user (the super admin is created in RolePermissionSeeder)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed forum data
        $this->call([
            ForumSeeder::class,
        ]);
    }
}
