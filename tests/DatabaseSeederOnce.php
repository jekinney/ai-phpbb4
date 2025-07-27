<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;

trait OptimizedTestDatabase
{
    use DatabaseTransactions;
    
    protected static $seeded = false;
    protected static $migrated = false;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations only once per test run
        if (!static::$migrated) {
            $this->artisan('migrate:fresh');
            static::$migrated = true;
        }
        
        // Seed essential data only once per test run
        if (!static::$seeded) {
            Artisan::call('db:seed', ['--class' => 'ConfigBasedRolePermissionSeeder']);
            static::$seeded = true;
        }
    }

    protected function seedForumData(): void
    {
        // Only seed forum data when specifically needed
        if (!isset(static::$forumSeeded)) {
            Artisan::call('db:seed', ['--class' => 'ForumSeeder']);
            static::$forumSeeded = true;
        }
    }
}
