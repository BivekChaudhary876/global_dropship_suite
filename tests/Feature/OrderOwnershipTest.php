<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $order = Order::create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'shipping_address' => '1 Test St',
            'total' => 50,
        ]);

        $response = $this->actingAs($intruder)->get("/orders/{$order->id}");

        $response->assertForbidden();
    }

    public function test_the_owner_can_view_their_own_order(): void
    {
        $owner = User::factory()->create();

        $order = Order::create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'shipping_address' => '1 Test St',
            'total' => 50,
        ]);

        $response = $this->actingAs($owner)->get("/orders/{$order->id}");

        $response->assertOk();
    }
}