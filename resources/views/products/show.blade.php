@extends('layouts.app')
@section('title', $product->name)
@section('content')
    <a href="{{ route('products.index') }}">&larr; Back to shop</a>

    <div style="background:#fff;padding:1.5rem;border-radius:8px;margin-top:1rem;">
        @if ($product->image_path)
            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" style="max-width:300px;border-radius:8px;">
        @endif
        <h1>{{ $product->name }}</h1>
        <p>{{ $product->description }}</p>
        <p>
            <strong>${{ number_format($product->price, 2) }} AUD</strong>
            @if ($converted && $converted['USD'] && $converted['EUR'])
                &middot; approx. ${{ number_format($product->price * $converted['USD'], 2) }} USD
                / &euro;{{ number_format($product->price * $converted['EUR'], 2) }} EUR
                <small style="color:#6b7280;">(live rate via open.er-api.com)</small>
            @endif
        </p>
        <p>Category: {{ $product->category->name }} &middot; Supplier: {{ $product->supplier->name }}</p>
        <p>Stock: {{ $product->stock_quantity }}</p>
        @if ($product->tags->count())
            <p>
                @foreach ($product->tags as $tag)
                    <span style="background:#e0e7ff;color:#3730a3;padding:0.15rem 0.5rem;border-radius:999px;font-size:0.8rem;margin-right:0.25rem;">{{ $tag->name }}</span>
                @endforeach
            </p>
        @endif
        @if ($product->reviews->count())
            <p>
                @include('components.star-rating', ['rating' => $product->reviews->avg('rating')])
                <span style="color:#6b7280;">({{ $product->reviews->count() }} review{{ $product->reviews->count() === 1 ? '' : 's' }})</span>
            </p>
        @else
            <p style="color:#6b7280;">No ratings yet.</p>
        @endif
       @auth
            <div style="margin:0.5rem 0;">
                <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ max($product->stock_quantity,1) }}" style="width:80px;display:inline;">
                <button type="button" class="btn"
                    onclick="addToCartAjax('{{ route('cart.add', $product) }}', document.getElementById('qty-{{ $product->id }}').value, this)">
                    Add to cart
                </button>
            </div>
            <div id="cart-flash" style="display:none;background:#111827;color:#fff;padding:0.4rem 0.8rem;border-radius:6px;margin-top:0.5rem;"></div>
        @endauth

        @auth
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.products.edit', $product) }}" class="btn">Edit</a>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline;"
                      onsubmit="return confirm('Delete this product?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn" style="background:#dc2626;">Delete</button>
                </form>
            @endif
        @endauth
    </div>
    <div style="background:#fff;padding:1.5rem;border-radius:8px;margin-top:1rem;">
        <h2>Reviews</h2>
        @forelse ($product->reviews as $review)
            <div style="border-bottom:1px solid #e5e7eb;padding:0.5rem 0;">
                <strong>{{ $review->user->name }}</strong>
                @include('components.star-rating', ['rating' => $review->rating])
                <p>{{ $review->comment }}</p>
                @auth
                    @if (auth()->id() === $review->user_id || auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                            onsubmit="return confirm('Delete your review?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;">Delete review</button>
                        </form>
                    @endif
                @endauth
            </div>
        @empty
            <p>No reviews yet.</p>
        @endforelse

        @auth
            <form method="POST" action="{{ route('reviews.store', $product) }}" style="margin-top:1rem;max-width:400px;">
                @csrf
                <label>Rating (1-5)</label>
                <input type="number" name="rating" min="1" max="5" required>
                <label>Comment</label>
                <textarea name="comment" maxlength="1000" style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #d1d5db;border-radius:6px;"></textarea>
                <button type="submit" class="btn" style="margin-top:0.5rem;">Submit review</button>
            </form>
        @else
            <p><a href="{{ route('login') }}">Login</a> to leave a review.</p>
        @endauth
    </div>
@endsection