@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Forgot your password?</h1>
    <p class="text-muted mb-md">Enter your email and we'll send you a reset link.</p>
    <form method="POST" action="{{ route('password.email') }}" class="panel">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        <button type="submit" class="btn-signal submit-btn-full">Send reset link</button>
    </form>
    <p class="mt-md"><a href="{{ route('login') }}" class="login-prompt-link">Back to login</a></p>
</div>
@endsection