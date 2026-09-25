@extends('layouts.app')
@section('title', 'Your Cart')
@section('content')
<div class="page-pad cart-page" data-page="cart">
    <h1 class="mb-md">Your cart</h1>

    @if ($products->isEmpty())
        <x-empty-state title="Your cart is empty." :action="route('products.index')" actionLabel="Continue shopping" />
    @else
        <div class="panel panel-flush">
            @foreach ($products as $product)
                <div class="cart-row">
                    <div>
                        <div class="cart-row-name">{{ $product->name }}</div>
                        <div class="cart-row-meta">Qty: {{ $product->cart_quantity }} &times; {{ money($product->price) }}</div>
                    </div>
                    <div class="cart-row-actions">
                        <span class="card-price">{{ money($product->price * $product->cart_quantity) }}</span>
                        <form method="POST" action="{{ route('cart.remove', $product) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="cart-total-row">
            <span class="total-display">Total: {{ money($total) }}</span>
        </div>
        <a href="{{ route('checkout.form') }}" class="btn-signal submit-btn-full">Proceed to checkout</a>
    @endif
</div>
@endsection