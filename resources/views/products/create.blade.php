@extends('layouts.app')
@section('title', 'Add Product')
@section('content')
<div class="page-pad narrow-form-wrap">
    <h1 class="mb-md">Add product</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="panel">
        @csrf
        @include('products._form')
        <button type="submit" class="btn-signal submit-btn-full">Create product</button>
    </form>
</div>
@endsection