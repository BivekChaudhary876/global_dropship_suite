<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Confirms the home route ("/") loads the shop page directly,
     * rendering the product listing rather than redirecting to it.
     */
    public function test_the_home_page_loads_the_shop_directly(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Shop');
    }

    /**
     * Confirms the shop page itself loads successfully for any visitor,
     * logged in or not.
     */
    public function test_the_shop_page_loads_successfully(): void
    {
        $response = $this->get('/products');

        $response->assertOk();
    }
}