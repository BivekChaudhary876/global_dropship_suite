@extends('layouts.app')
@section('title', 'Edit Tag')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Edit Tag</h1>
    <form method="POST" action="{{ route('admin.tags.update', $tag) }}" class="panel">
        @csrf
        @method('PATCH')
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $tag->name) }}" placeholder="Tag name" required>
        <button type="submit" class="btn-signal submit-btn-full">Save Changes</button>
    </form>
    @error('name')
        <p class="text-muted">{{ $message }}</p>
    @enderror
</div>
@endsection