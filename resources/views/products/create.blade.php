@extends('layouts.app')
@section('title', 'Add Product')
@section('content')
    <h1>Add Product</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" style="max-width:500px;">
        @csrf
        @include('products._form')
        <button type="submit" class="btn" style="margin-top:1rem;">Create Product</button>
    </form>
@endsection