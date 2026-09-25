@props(['product', 'delay' => 0, 'feature' => false])
<div class="card reveal {{ $feature ? 'feature' : '' }}" style="transition-delay: {{ $delay }}s"
     onclick="location.href='{{ route('products.show', $product) }}'">
    <div class="card-art" style="view-transition-name: product-photo-{{ $product->id }};">
        @if($product->image_path)
            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}">
        @else
            <div class="card-art-placeholder">{{ initials($product->name) }}</div>
        @endif
        <span class="quick-view">Quick view</span>
    </div>
    <div class="card-body">
        <div class="card-cat">{{ $product->category->name }}</div>
        <div class="card-name">{{ $product->name }}</div>
        @if($product->reviews->count())
            <x-star-rating :rating="$product->reviews->avg('rating')" />
        @endif
        <div class="card-price-row">
            <span class="card-price">{{ money($product->price) }}</span>
            <span class="pill">{{ $product->stock_quantity }} in stock</span>
        </div>
    </div>
</div>