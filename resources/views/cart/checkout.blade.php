@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
    <h1>Checkout</h1>
    <div style="background:#fff;padding:1rem;border-radius:8px;">
        <h3>Order summary</h3>
        <ul>
            @foreach ($products as $product)
                <li>{{ $product->name }} &times; {{ $cart[$product->id] }} = ${{ number_format($product->price * $cart[$product->id], 2) }}</li>
            @endforeach
        </ul>
    </div>
    <form method="POST" action="{{ route('checkout.store') }}" style="max-width:400px;margin-top:1rem;">
        @csrf
        <label>Shipping address</label>
        <input type="text" name="shipping_address" required value="{{ old('shipping_address') }}">
        <button type="submit" class="btn" style="margin-top:1rem;">Place Order</button>
    </form>
@endsection