@extends('layouts.app')
@section('title', 'Add User')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Add user</h1>
    <form method="POST" action="{{ route('admin.users.store') }}" class="panel">
        @csrf
        @include('admin.users._form')
        <button type="submit" class="btn-signal submit-btn-full">Create user</button>
    </form>
</div>
@endsection