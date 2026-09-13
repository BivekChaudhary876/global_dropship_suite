@extends('layouts.app')
@section('title', 'Order #'.$order->id)
@section('content')
    <a href="{{ route('orders.index') }}">&larr; Back to orders</a>
    <div style="background:#fff;padding:1.5rem;border-radius:8px;margin-top:1rem;">
        <h1>Order #{{ $order->id }}</h1>
        <p>Status: <strong>{{ ucfirst($order->status) }}</strong></p>
        <p>Shipping address: {{ $order->shipping_address }}</p>
        <table style="width:100%;border-collapse:collapse;">
            <tr><th style="text-align:left;">Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </table>
        <p style="margin-top:1rem;"><strong>Total: ${{ number_format($order->total, 2) }}</strong></p>
    </div>
@endsection