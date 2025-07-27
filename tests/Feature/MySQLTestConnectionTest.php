<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MySQLTestConnectionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function mysql_test_database_connection_works()
    {
        // Test that we can connect to MySQL test database
        $this->assertNotNull(DB::connection());
        
        // Test that we're using MySQL
        $this->assertEquals('mysql', DB::connection()->getDriverName());
        
        // Test that we're using the test database
        $this->assertEquals('laravel_test', DB::connection()->getDatabaseName());
        
        // Test basic query execution
        $result = DB::select('SELECT 1 as test');
        $this->assertEquals(1, $result[0]->test);
    }

    /** @test */
    public function database_migrations_work()
    {
        // Run migrations to ensure they work
        $this->artisan('migrate');
        
        // Check that some key tables exist
        $this->assertTrue(DB::connection()->getSchemaBuilder()->hasTable('users'));
        $this->assertTrue(DB::connection()->getSchemaBuilder()->hasTable('roles'));
        $this->assertTrue(DB::connection()->getSchemaBuilder()->hasTable('permissions'));
    }
}
