@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Edit user</h1>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="panel">
        @csrf @method('PUT')
        @include('admin.users._form')
        <button type="submit" class="btn-signal submit-btn-full">Save changes</button>
    </form>
</div>
@endsection