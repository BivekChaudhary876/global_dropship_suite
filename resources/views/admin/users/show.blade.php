@extends('layouts.app')
@section('title', $user->name)
@section('content')
<div class="page-pad">
    <a href="{{ route('admin.users.index') }}" class="back-link">&larr; Back to users</a>

    <div class="panel mt-md">
        <h1 class="mb-md">{{ $user->name }}</h1>
        <table class="spec-table">
            <tr><td>Email</td><td>{{ $user->email }}</td></tr>
            <tr><td>Role</td><td>{{ ucfirst($user->role) }}</td></tr>
            <tr><td>Joined</td><td>{{ $user->created_at->format('d M Y, g:ia') }}</td></tr>
            <tr><td>Last updated</td><td>{{ $user->updated_at->format('d M Y, g:ia') }}</td></tr>
            <tr><td>Total orders</td><td>{{ $user->orders->count() }}</td></tr>
            <tr><td>Total reviews</td><td>{{ $user->reviews->count() }}</td></tr>
        </table>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn-ghost mt-md">Edit / reset password</a>
    </div>

    <div class="panel panel-spaced">
        <h2 class="section-title">Order history</h2>
        @forelse ($user->orders as $order)
            <div class="cart-row">
                <div>
                    <div class="cart-row-name">Order #{{ $order->id }}</div>
                    <div class="cart-row-meta">{{ $order->created_at->format('d M Y') }}</div>
                </div>
                <div class="cart-row-actions">
                    <x-status-badge :status="$order->status" />
                    <a href="{{ route('orders.show', $order) }}" class="login-prompt-link">View</a>
                </div>
            </div>
        @empty
            <p class="text-muted">No orders yet.</p>
        @endforelse
    </div>
</div>
@endsection