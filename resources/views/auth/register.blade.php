@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Create an account</h1>
    <form method="POST" action="{{ route('register') }}" class="panel">
        @csrf
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required autofocus>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Confirm password</label>
        <input type="password" name="password_confirmation" required>
        <button type="submit" class="btn-signal submit-btn-full mt-lg">Register</button>
    </form>
    <p class="mt-md"><a href="{{ route('login') }}" class="login-prompt-link">Already have an account? Login</a></p>
</div>
@endsection