@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="page-pad checkout-page">
    <div class="checkout-steps">
        @foreach (['Cart', 'Shipping', 'Confirm'] as $i => $step)
            <div class="step">
                <div class="step-circle {{ $i <= 1 ? 'is-active' : 'is-inactive' }}">{{ $i + 1 }}</div>
                <span class="step-label">{{ $step }}</span>
            </div>
            @if (!$loop->last)
                <div class="step-connector {{ $i < 1 ? 'is-active' : 'is-inactive' }}"></div>
            @endif
        @endforeach
    </div>

    <h1 class="mb-md">Checkout</h1>
    <div class="panel">
        <h2 class="section-title">Order summary</h2>
        @foreach ($products as $product)
            <div class="order-summary-row">
                <span>{{ $product->name }} &times; {{ $cart[$product->id] }}</span>
                <span>{{ money($product->price * $cart[$product->id]) }}</span>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('checkout.store') }}" class="panel panel-spaced">
        @csrf
        <label>Shipping address</label>
        <input type="text" name="shipping_address" required value="{{ old('shipping_address') }}">
        <button type="submit" class="btn-signal submit-btn-full">Place order</button>
    </form>
</div>
@endsection