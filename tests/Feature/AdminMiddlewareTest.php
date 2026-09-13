<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_cannot_reach_the_admin_product_creation_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin/products/create');

        $response->assertForbidden();
    }

    public function test_a_guest_is_redirected_away_from_admin_routes(): void
    {
        $response = $this->get('/admin/products/create');

        $response->assertRedirect('/login');
    }

    public function test_an_admin_can_reach_the_admin_product_creation_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/products/create');

        $response->assertOk();
    }
}