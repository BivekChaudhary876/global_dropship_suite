@extends('layouts.app')
@section('title', 'Shop')
@section('content')
    <h1>Shop</h1>
    <form method="GET" action="{{ route('products.index') }}" style="display:flex;gap:0.5rem;margin-bottom:1rem;">
        <input type="text" name="q" placeholder="Search products..." value="{{ request('q') }}">
        <select name="category_id" style="width:auto;">
            <option value="">All categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn">Filter</button>
    </form>
    @auth
        @if (auth()->user()->isAdmin())
            <p><a href="{{ route('admin.products.create') }}" class="btn">+ Add Product</a></p>
        @endif
    @endauth

    @foreach ($products as $product)
        <div style="background:#fff;padding:1rem;border-radius:8px;margin-bottom:0.75rem;">
            <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
            <p>{{ $product->category->name }} &middot; ${{ number_format($product->price, 2) }}</p>
        </div>
    @endforeach

    {{ $products->links() }}
@endsection