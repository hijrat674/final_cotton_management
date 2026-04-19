<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_root_route_redirects_guests_to_the_login_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_the_login_page_loads_successfully(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }
}
