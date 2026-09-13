@extends('layouts.app')
@section('title', 'Orders')
@section('content')
    <h1>{{ auth()->user()->isAdmin() ? 'All Orders' : 'My Orders' }}</h1>
    <table style="width:100%;border-collapse:collapse;background:#fff;">
        <tr>
            <th style="text-align:left;padding:0.5rem;">#</th>
            @if(auth()->user()->isAdmin())<th>Customer</th>@endif
            <th>Status</th><th>Total</th><th></th>
        </tr>
        @forelse ($orders as $order)
            <tr>
                <td style="padding:0.5rem;">{{ $order->id }}</td>
                @if(auth()->user()->isAdmin())<td>{{ $order->user->name }}</td>@endif
                <td>{{ ucfirst($order->status) }}</td>
                <td>${{ number_format($order->total, 2) }}</td>
                <td><a href="{{ route('orders.show', $order) }}">View</a></td>
            </tr>
        @empty
            <tr><td colspan="5" style="padding:0.5rem;">No orders yet.</td></tr>
        @endforelse
    </table>
    {{ $orders->links() }}
@endsection