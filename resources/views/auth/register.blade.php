@extends('layouts.app')
@section('title', 'Register')
@section('content')
    <h1>Create an account</h1>
    <form method="POST" action="{{ route('register') }}" style="max-width:400px;">
        @csrf
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required>
        <button type="submit" class="btn" style="margin-top:1rem;">Register</button>
    </form>
    <p><a href="{{ route('login') }}">Already have an account? Login</a></p>
@endsection