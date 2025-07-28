<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestPmBanSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pm-ban';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the PM ban system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing PM Ban System...');
        
        // Create or get a test user
        $user = User::firstOrCreate(
            ['email' => 'testuser@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->info("Testing with user: {$user->name} (ID: {$user->id})");

        // Test 1: Check if user can send messages initially
        $this->info("\n--- Test 1: Initial State ---");
        $this->info("Can send messages: " . ($user->canSendMessages() ? 'YES' : 'NO'));
        $this->info("Can receive messages: " . ($user->canReceiveMessages() ? 'YES' : 'NO'));
        $this->info("Is PM banned: " . ($user->isPmBanned() ? 'YES' : 'NO'));

        // Create or get admin user for testing
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Test 2: Ban the user temporarily (1 hour)
        $this->info("\n--- Test 2: Temporary Ban (1 hour) ---");
        $expiresAt = now()->addHour();
        $user->pmBan($adminUser, 'Testing temporary ban', $expiresAt);
        $user->refresh();
        
        $this->info("Can send messages: " . ($user->canSendMessages() ? 'YES' : 'NO'));
        $this->info("Can receive messages: " . ($user->canReceiveMessages() ? 'YES' : 'NO'));
        $this->info("Is PM banned: " . ($user->isPmBanned() ? 'YES' : 'NO'));
        $this->info("Ban reason: " . $user->pm_ban_reason);
        $this->info("Ban expires: " . $user->pm_ban_expires_at);

        // Test 3: Remove the ban
        $this->info("\n--- Test 3: Remove Ban ---");
        $user->removePmBan();
        $user->refresh();
        
        $this->info("Can send messages: " . ($user->canSendMessages() ? 'YES' : 'NO'));
        $this->info("Can receive messages: " . ($user->canReceiveMessages() ? 'YES' : 'NO'));
        $this->info("Is PM banned: " . ($user->isPmBanned() ? 'YES' : 'NO'));

        // Test 4: Permanent ban
        $this->info("\n--- Test 4: Permanent Ban ---");
        $user->pmBan($adminUser, 'Testing permanent ban'); // null = permanent
        $user->refresh();
        
        $this->info("Can send messages: " . ($user->canSendMessages() ? 'YES' : 'NO'));
        $this->info("Can receive messages: " . ($user->canReceiveMessages() ? 'YES' : 'NO'));
        $this->info("Is PM banned: " . ($user->isPmBanned() ? 'YES' : 'NO'));
        $this->info("Ban reason: " . $user->pm_ban_reason);
        $this->info("Ban expires: " . ($user->pm_ban_expires_at ? $user->pm_ban_expires_at : 'NEVER (Permanent)'));

        // Test 5: Clean up
        $this->info("\n--- Test 5: Cleanup ---");
        $user->removePmBan();
        $user->refresh();
        
        $this->info("Final state - Can send messages: " . ($user->canSendMessages() ? 'YES' : 'NO'));

        $this->info("\n✅ PM Ban System tests completed!");
        
        return 0;
    }
}
