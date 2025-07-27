<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Home page should be accessible to everyone
        $response->assertStatus(200);
    }
}
