<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A simple starter test - confirms the home route correctly redirects
     * a visitor to the shop page, since "/" itself has no view of its own.
     */
    public function test_the_home_page_redirects_to_the_shop(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/products');
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