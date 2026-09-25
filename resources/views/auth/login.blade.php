@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Login</h1>
    <form method="POST" action="{{ route('login') }}" class="panel">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn-signal submit-btn-full mt-lg">Login</button>
    </form>
    <p class="mt-md"><a href="{{ route('register') }}" class="login-prompt-link">No account? Register</a></p>
    <p class="text-muted">Demo admin: admin@dropship.test / password</p>
</div>
@endsection