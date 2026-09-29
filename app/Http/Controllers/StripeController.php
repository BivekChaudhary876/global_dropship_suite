<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    private function assertOwner(Order $order): void
    {
        abort_unless(auth()->id() === $order->user_id || auth()->user()->isAdmin(), 403);
    }

    public function checkout(Order $order): RedirectResponse
    {
        $this->assertOwner($order);

        if ($order->status !== OrderStatus::Pending) {
            return redirect()->route('orders.show', $order)
                ->with('status', 'This order has already been '.$order->status->label().'.');
        }

        $order->load('items.product');

        $lineItems = [];
        foreach ($order->items as $index => $item) {
            $lineItems[$index] = [
                'quantity' => $item->quantity,
                'price_data' => [
                    'currency' => 'aud',
                    'unit_amount' => (int) round($item->unit_price * 100),
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                ],
            ];
        }

        $response = Http::asForm()
            ->withToken(config('services.stripe.secret'))
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'line_items' => $lineItems,
                'client_reference_id' => $order->id,
                'success_url' => route('stripe.success', $order).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel', $order),
            ]);

        if ($response->failed()) {
            Log::error('Stripe checkout session creation failed', ['body' => $response->json()]);

            return redirect()->route('orders.show', $order)
                ->with('status', 'Could not start payment - check STRIPE_SECRET in your .env and try again.');
        }

        $session = $response->json();

        $order->update(['stripe_session_id' => $session['id']]);

        return redirect()->away($session['url']);
    }

    public function success(Request $request, Order $order): RedirectResponse
    {
        $this->assertOwner($order);

        $sessionId = $request->query('session_id');

        if (! $sessionId || $sessionId !== $order->stripe_session_id) {
            abort(403, 'This payment confirmation does not match this order.');
        }

        $response = Http::withToken(config('services.stripe.secret'))
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        if ($response->failed()) {
            Log::error('Stripe session lookup failed', ['body' => $response->json()]);

            return redirect()->route('orders.show', $order)
                ->with('status', 'Could not confirm payment with Stripe. Please contact support.');
        }

        $session = $response->json();

        if (($session['payment_status'] ?? null) === 'paid'
            && (int) ($session['client_reference_id'] ?? 0) === $order->id) {
            $order->update(['status' => OrderStatus::Paid]);

            return redirect()->route('orders.show', $order)
                ->with('status', 'Payment successful! Your order is now marked as paid.');
        }

        return redirect()->route('orders.show', $order)
            ->with('status', 'Stripe has not confirmed this payment yet. If you completed checkout, please refresh in a moment.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->assertOwner($order);

        return redirect()->route('orders.show', $order)
            ->with('status', 'Payment was cancelled. Your order is still pending - you can try paying again anytime.');
    }
}