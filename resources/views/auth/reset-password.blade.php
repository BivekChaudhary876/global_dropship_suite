@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Set a new password</h1>
    <form method="POST" action="{{ route('password.update') }}" class="panel">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $email) }}" required>
        <label>New password</label>
        <input type="password" name="password" required>
        <label>Confirm new password</label>
        <input type="password" name="password_confirmation" required>
        <button type="submit" class="btn-signal submit-btn-full">Reset password</button>
    </form>
</div>
@endsection