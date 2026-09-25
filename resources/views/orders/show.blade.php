@extends('layouts.app')
@section('title', 'Order #'.$order->id)
@section('content')
<div class="page-pad">
    <a href="{{ route('orders.index') }}" class="back-link">&larr; Back to orders</a>

    <div class="panel mt-md">
        <h1 class="mb-md">Order #{{ $order->id }}</h1>

        @if (auth()->user()->isAdmin())
            <div class="status-select-row">
                <label>Status</label>
                <select id="order-status-select" data-order-id="{{ $order->id }}">
                    @foreach (\App\Enums\OrderStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected($order->status->value === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <span id="order-status-badge"><x-status-badge :status="$order->status" /></span>
            </div>
        @else
            <div class="mb-md"><x-status-badge :status="$order->status" /></div>
        @endif

        <p class="order-meta">Shipping address: {{ $order->shipping_address }}</p>

        <table>
            <thead><tr><th>Product</th><th>Qty</th><th>Unit price</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ money($item->unit_price) }}</td>
                        <td>{{ money($item->unit_price * $item->quantity) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="order-total-display">Total: {{ money($order->total) }}</p>
    </div>
</div>
@endsection

@if (auth()->user()->isAdmin())
@section('scripts')
<script type="module">
    import { initOrderStatusUpdater } from "{{ asset('js/app.js') }}";
    initOrderStatusUpdater();
</script>
@endsection
@endif