@extends('layouts.app')
@section('title', 'Edit Category')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Edit category</h1>
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="panel">
        @csrf
        @method('PATCH')
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
        <button type="submit" class="btn-signal submit-btn-full">Save changes</button>
    </form>
</div>
@endsection