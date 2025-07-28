<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users if they don't exist
        $testUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        $userRole = Role::where('name', 'user')->first();

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign user role if not already assigned
            if ($userRole && !$user->roles()->where('role_id', $userRole->id)->exists()) {
                $user->roles()->attach($userRole->id);
            }

            $this->command->info("Created/updated user: {$user->email}");
        }
    }
}
