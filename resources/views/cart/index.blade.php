@extends('layouts.app')
@section('title', 'Your Cart')
@section('content')
    <h1>Your Cart</h1>
    @if ($products->isEmpty())
        <p>Your cart is empty. <a href="{{ route('products.index') }}">Continue shopping</a>.</p>
    @else
        <table style="width:100%;border-collapse:collapse;background:#fff;">
            <tr><th style="text-align:left;padding:0.5rem;">Product</th><th>Qty</th><th>Subtotal</th><th></th></tr>
            @foreach ($products as $product)
                <tr>
                    <td style="padding:0.5rem;">{{ $product->name }}</td>
                    <td>{{ $product->cart_quantity }}</td>
                    <td>${{ number_format($product->price * $product->cart_quantity, 2) }}</td>
                    <td>
                        <form method="POST" action="{{ route('cart.remove', $product) }}">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
        <p style="margin-top:1rem;"><strong>Total: ${{ number_format($total, 2) }}</strong></p>
        <a href="{{ route('checkout.form') }}" class="btn">Proceed to Checkout</a>
    @endif
@endsection