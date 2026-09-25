@extends('layouts.app')
@section('title', 'Shop')
@section('content')

@if (!request()->hasAny(['q', 'category_id']) && request()->routeIs(['home', 'products.index']) && $products->currentPage() === 1)
<section class="hero">
    <div class="hero-copy">
        <div class="kicker">Curated dropship catalogue</div>
        <h1>Goods worth shipping twice.</h1>
        <p>Every product on Global DropShip is vetted by an actual seller before it reaches your customers — sourced, priced, and ready to sell in minutes.</p>
        <div class="hero-cta">
            <a href="#trending" class="btn-signal">Browse the catalogue</a>
            <a href="{{ route('register') }}" class="btn-ghost">How it works</a>
        </div>
        <div class="hero-stats">
            <div><strong>{{ number_format($stats['products']) }}+</strong><span>Live products</span></div>
            <div><strong>{{ $stats['suppliers'] }}</strong><span>Verified suppliers</span></div>
            <div><strong>{{ $stats['avgRating'] ?: '—' }}</strong><span>Avg. seller rating</span></div>
        </div>
    </div>
    <div class="hero-art">
        <div class="art-card tall">
            <span class="pill">Bestseller</span>
            <svg viewBox="0 0 120 100" fill="none"><ellipse cx="60" cy="80" rx="45" ry="8" fill="var(--ink)" opacity="0.08"/><path d="M15 70 Q10 40 35 30 Q45 15 70 20 Q95 22 100 45 Q105 65 85 72 Q50 82 15 70Z" stroke="var(--ink)" stroke-width="2.5" fill="none"/><path d="M35 30 Q50 45 70 20" stroke="var(--ink)" stroke-width="2" fill="none"/><circle cx="55" cy="50" r="3" fill="var(--signal)"/></svg>
        </div>
        <div class="art-card">
            <svg viewBox="0 0 100 80" fill="none"><rect x="20" y="15" width="60" height="45" rx="4" stroke="var(--ink)" stroke-width="2.5"/><circle cx="50" cy="37" r="12" stroke="var(--signal)" stroke-width="2.5"/><line x1="20" y1="65" x2="80" y2="65" stroke="var(--ink)" stroke-width="2.5"/></svg>
        </div>
        <div class="art-card">
            <svg viewBox="0 0 100 80" fill="none"><path d="M30 60 L30 30 Q30 15 50 15 Q70 15 70 30 L70 60" stroke="var(--ink)" stroke-width="2.5" fill="none"/><rect x="22" y="55" width="16" height="18" rx="2" stroke="var(--moss)" stroke-width="2.5"/><rect x="62" y="55" width="16" height="18" rx="2" stroke="var(--moss)" stroke-width="2.5"/></svg>
        </div>
    </div>
</section>
@endif

<div class="page-pad">
    <div class="cat-rail">
        <a href="{{ route('products.index') }}" class="cat-chip {{ !request('category_id') ? 'on' : '' }}">All</a>
        @foreach ($categories as $cat)
            <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="cat-chip {{ request('category_id') == $cat->id ? 'on' : '' }}">{{ $cat->name }}</a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('products.index') }}" style="display:flex; gap:0.75rem; margin-bottom:1rem; flex-wrap:wrap;">
        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
        <input type="text" name="q" placeholder="Search products..." value="{{ request('q') }}" style="max-width:280px; margin-top:0;">
        <button type="submit" class="btn-ghost">Filter</button>
    </form>

    <div class="section-head" id="trending">
        <h2>{{ request('q') || request('category_id') ? 'Results' : 'Trending this week' }}</h2>
        @if(request()->hasAny(['q', 'category_id']))
            <a href="{{ route('products.index') }}">Clear filters</a>
        @endif
    </div>

    @if ($products->isEmpty())
        <x-empty-state title="No products match your search." :action="route('products.index')" actionLabel="Clear filters" />
    @else
        <div class="grid">
            @foreach ($products as $i => $product)
                <x-product-card :product="$product" :delay="min($i, 12) * 0.05" :feature="$i === 0 && $products->currentPage() === 1" />
            @endforeach
        </div>
        <div class="mt-lg">{{ $products->links() }}</div>
    @endif
</div>
@endsection