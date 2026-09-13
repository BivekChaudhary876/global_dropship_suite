@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <h1>Login</h1>
    <form method="POST" action="{{ route('login') }}" style="max-width:400px;">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn" style="margin-top:1rem;">Login</button>
    </form>
    <p><a href="{{ route('register') }}">No account? Register</a></p>
    <p style="font-size:0.85rem;color:#6b7280;">Demo admin: admin@dropship.test / password</p>
@endsection