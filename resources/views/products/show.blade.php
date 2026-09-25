@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="page-pad">
    <a href="{{ route('products.index') }}" class="back-link">&larr; Back to shop</a>

    <div class="showcase-grid">
        <div class="thumb-rail">
            <div class="thumb on" id="pd-thumb-main">
                @if ($product->image_path)
                    <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <div class="card-art-placeholder">{{ initials($product->name) }}</div>
                @endif
            </div>
        </div>

        <div id="pd-zoom-wrap" class="detail-image" style="view-transition-name: product-photo-{{ $product->id }};">
            @if ($product->image_path)
                <img id="pd-zoom-img" src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}">
            @else
                <div class="card-art-placeholder detail-placeholder">{{ initials($product->name) }}</div>
            @endif
        </div>

        <div class="showcase-info">
            <div class="card-cat">{{ $product->category->name }} &middot; sold by {{ $product->supplier->name }}</div>
            <h1 class="detail-title">{{ $product->name }}</h1>

            @if ($product->reviews->count())
                <div class="detail-rating-row">
                    <x-star-rating :rating="$product->reviews->avg('rating')" />
                    <span class="detail-rating-count">({{ $product->reviews->count() }} review{{ $product->reviews->count() === 1 ? '' : 's' }})</span>
                </div>
            @else
                <p class="detail-no-rating">No ratings yet.</p>
            @endif

            @if ($product->tags->count())
                <div class="tag-list">
                    @foreach ($product->tags as $tag)
                        <x-tag-pill>{{ $tag->name }}</x-tag-pill>
                    @endforeach
                </div>
            @endif

            <table class="spec-table">
                <tr><td>Category</td><td>{{ $product->category->name }}</td></tr>
                <tr><td>Supplier</td><td>{{ $product->supplier->name }}</td></tr>
                <tr><td>Stock</td><td>{{ $product->stock_quantity }} available</td></tr>
            </table>

            @if ($product->description)
                <h2 class="section-title">About this item</h2>
                <ul class="about-list">
                    @foreach (explode("\n", trim($product->description)) as $line)
                        @if (trim($line))
                            <li>{{ trim($line) }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif
           
        </div>

        <div class="buy-box">
            <div class="price-display">{{ money($product->price) }} AUD</div>
            @if ($converted && $converted['USD'] && $converted['EUR'])
                <p class="price-converted">
                    &approx; {{ money($product->price * $converted['USD']) }} USD /
                    &euro;{{ number_format($product->price * $converted['EUR'], 2) }} EUR
                </p>
            @endif

            @if ($product->stock_quantity > 10)
                <div class="stock-indicator stock-in"><span class="dot"></span> In stock</div>
            @elseif ($product->stock_quantity > 0)
                <div class="stock-indicator stock-low"><span class="dot"></span> Only {{ $product->stock_quantity }} left</div>
            @else
                <div class="stock-indicator stock-out"><span class="dot"></span> Out of stock</div>
            @endif

            <div class="trust-row">
                <div class="trust-item"><span class="icon">&#128274;</span>Secure checkout</div>
                <div class="trust-item"><span class="icon">&#128230;</span>Fast delivery</div>
                <div class="trust-item"><span class="icon">&#8635;</span>Easy returns</div>
            </div>

            @auth
                @if ($product->stock_quantity > 0)
                    <div class="add-to-cart-row">
                        <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" class="qty-input">
                        <button type="button" class="btn-signal submit-btn-full" style="margin-top:0;"
                            onclick="rippleOn(this, event); addToCartAjax('{{ route('cart.add', $product) }}', document.getElementById('qty-{{ $product->id }}').value, this)">
                            Add to cart
                        </button>
                    </div>
                @else
                    <button class="btn-ghost submit-btn-full" disabled>Out of stock</button>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-signal submit-btn-full" style="text-align:center;">Login to buy</a>
            @endauth

            @auth
                @if (auth()->user()->isAdmin())
                    <div class="admin-actions-row">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-ghost">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <div class="panel panel-spaced">
        <h2 class="section-title">Reviews</h2>
        @forelse ($product->reviews as $review)
            <div class="review-item">
                <span class="avatar">{{ initials($review->user->name) }}</span>
                <div class="review-body">
                    <div class="review-header">
                        <strong>{{ $review->user->name }}</strong>
                        <x-star-rating :rating="$review->rating" />
                    </div>
                    <p class="review-comment">{{ $review->comment }}</p>
                    @auth
                        @if (auth()->id() === $review->user_id || auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Delete your review?');" class="review-delete-btn">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger">Delete review</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        @empty
            <p class="text-muted">No reviews yet.</p>
        @endforelse

        @auth
            <form method="POST" action="{{ route('reviews.store', $product) }}" class="review-form">
                @csrf
                <label>Rating (1-5)</label>
                <input type="number" name="rating" min="1" max="5" required>
                <label>Comment</label>
                <textarea name="comment" maxlength="1000"></textarea>
                <button type="submit" class="btn-signal mt-sm">Submit review</button>
            </form>
        @else
            <p class="login-prompt"><a href="{{ route('login') }}">Login</a> to leave a review.</p>
        @endauth
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    import { initZoomLens } from "{{ asset('js/app.js') }}";
    initZoomLens();
</script>
@endsection