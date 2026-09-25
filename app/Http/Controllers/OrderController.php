<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = $user->isAdmin()
            ? Order::with('user')->latest()->paginate(10)
            : $user->orders()->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ownership authorization: block anyone who isn't the order's owner
        // or an admin - even if they craft the URL directly.
        abort_unless(auth()->id() === $order->user_id || auth()->user()->isAdmin(), 403);

        $order->load('items.product');
        return view('orders.show', compact('order'));
    }

    public function checkoutForm(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        return view('cart.checkout', compact('products', 'cart'));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'shipping_address' => ['required', 'string', 'max:255'],
        ]);

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        $order = DB::transaction(function () use ($request, $data, $cart, $products) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'],
                'total' => 0,
            ]);

            $total = 0;
            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    continue;
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                ]);
                $total += $product->price * $qty;
            }
            $order->update(['total' => $total]);

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('status', 'Order placed! #'.$order->id);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $order->update(['status' => $data['status']]);

        return response()->json([
            'status' => $order->status->value,
            'label' => $order->status->label(),
        ]);
    }
}