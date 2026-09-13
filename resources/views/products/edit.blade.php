@extends('layouts.app')
@section('title', 'Edit Product')
@section('content')
    <h1>Edit Product</h1>
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" style="max-width:500px;">
        @csrf @method('PATCH')
        @include('products._form')
        <button type="submit" class="btn" style="margin-top:1rem;">Save Changes</button>
    </form>
@endsection