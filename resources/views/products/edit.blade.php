@extends('layouts.app')
@section('title', 'Edit Product')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Edit product</h1>
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="panel">
        @csrf @method('PATCH')
        @include('products._form')
        <button type="submit" class="btn-signal submit-btn-full">Save changes</button>
    </form>
</div>
@endsection