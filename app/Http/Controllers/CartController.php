<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Cart lives in the session as [product_id => quantity] - no DB table needed.

    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()
            ->map(function (Product $p) use ($cart) {
                $p->cart_quantity = $cart[$p->id];
                return $p;
            });

        $total = $products->sum(fn ($p) => $p->price * $p->cart_quantity);

        return view('cart.index', compact('products', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.max($product->stock_quantity, 1)],
        ]);

        $cart = $request->session()->get('cart', []);
        $qty = $request->integer('quantity', 1);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $qty;
        $request->session()->put('cart', $cart);

        $count = array_sum($cart);

        if ($request->wantsJson()) {
            return response()->json(['message' => "{$product->name} added to cart.", 'count' => $count]);
        }

        return back()->with('status', "{$product->name} added to cart.");
    }

    public function remove(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Removed from cart.');
    }
}