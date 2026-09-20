@extends('layouts.app')
@section('title', 'Edit category')

@section('content')
<h1 class="h3 mb-3">Edit category</h1>
<form method="POST" action="{{ route('categories.update', $category) }}" class="bg-white p-4 rounded shadow-sm">
    @csrf
    @method('PUT')
    @include('categories._form')
    <button class="btn btn-primary">Save changes</button>
</form>
@endsection