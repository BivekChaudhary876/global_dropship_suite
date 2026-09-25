@extends('layouts.app')
@section('title', 'Orders')
@section('content')
<div class="page-pad">
    <h1 class="mb-md">{{ auth()->user()->isAdmin() ? 'All orders' : 'My orders' }}</h1>

    @if ($orders->isEmpty())
        <x-empty-state title="No orders yet." :action="route('products.index')" actionLabel="Start shopping" />
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @if (auth()->user()->isAdmin())<th>Customer</th>@endif
                    <th>Status</th><th>Total</th><th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        @if (auth()->user()->isAdmin())<td>{{ $order->user->name }}</td>@endif
                        <td><x-status-badge :status="$order->status" /></td>
                        <td>{{ money($order->total) }}</td>
                        <td><a href="{{ route('orders.show', $order) }}" class="login-prompt">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-lg">{{ $orders->links() }}</div>
    @endif
</div>
@endsection