<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    private function makePendingOrder(User $owner): Order
    {
        $product = Product::factory()->create(['price' => 25]);

        $order = Order::create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'shipping_address' => '1 Test St',
            'total' => 25,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 25,
        ]);

        return $order;
    }

    public function test_starting_checkout_creates_a_stripe_session_and_redirects_to_stripe(): void
    {
        $owner = User::factory()->create();
        $order = $this->makePendingOrder($owner);

        Http::fake([
            'api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_fake123',
                'url' => 'https://checkout.stripe.com/c/pay/cs_test_fake123',
            ], 200),
        ]);

        $response = $this->actingAs($owner)->get("/orders/{$order->id}/pay");

        $response->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_fake123');
        $this->assertSame('cs_test_fake123', $order->fresh()->stripe_session_id);
    }

    public function test_a_customer_cannot_start_payment_on_someone_elses_order(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $order = $this->makePendingOrder($owner);

        $response = $this->actingAs($intruder)->get("/orders/{$order->id}/pay");

        $response->assertForbidden();
    }

    public function test_success_callback_marks_the_order_paid_when_stripe_confirms_payment(): void
    {
        $owner = User::factory()->create();
        $order = $this->makePendingOrder($owner);
        $order->update(['stripe_session_id' => 'cs_test_fake123']);

        Http::fake([
            'api.stripe.com/v1/checkout/sessions/cs_test_fake123' => Http::response([
                'id' => 'cs_test_fake123',
                'payment_status' => 'paid',
                'client_reference_id' => (string) $order->id,
            ], 200),
        ]);

        $response = $this->actingAs($owner)
            ->get("/orders/{$order->id}/pay/success?session_id=cs_test_fake123");

        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame('paid', $order->fresh()->status->value);
    }

    public function test_success_callback_rejects_a_session_id_that_does_not_match_the_order(): void
    {
        $owner = User::factory()->create();
        $order = $this->makePendingOrder($owner);
        $order->update(['stripe_session_id' => 'cs_test_real_session']);

        $response = $this->actingAs($owner)
            ->get("/orders/{$order->id}/pay/success?session_id=cs_test_guessed");

        $response->assertForbidden();
        $this->assertSame('pending', $order->fresh()->status->value);
    }
}