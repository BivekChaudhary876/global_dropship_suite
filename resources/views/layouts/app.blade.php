<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
<title>@yield('title', 'Global DropShip Suite')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div id="nav-progress"></div>

<nav class="topbar">
    <a href="{{ route('home') }}" class="wordmark">Global<span>DropShip</span></a>

    <form method="GET" action="{{ route('products.index') }}" class="nav-search">
        <input type="text" name="q" placeholder="Search products..." value="{{ request('q') }}">
        <button type="submit" aria-label="Search">&#128269;</button>
    </form>

    <div class="navlinks">
        @auth
            <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">{{ auth()->user()->isAdmin() ? 'All Orders' : 'My Orders' }}</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.tags.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">Tags</a>
                <a href="{{ route('admin.products.create') }}">Add product</a>
            @endif
        @endauth
    </div>

    <div class="navicons">
        @auth
            <a href="{{ route('cart.index') }}" class="cart-pill" id="cart-pill">🛒 <span id="cart-count">{{ array_sum(session('cart', [])) }}</span></a>
            <div class="account-wrap nav-end">
                <button type="button" class="account-trigger" id="account-trigger">
                    <span class="avatar">{{ initials(auth()->user()->name) }}</span> {{ auth()->user()->name }}
                </button>
                <div class="account-dropdown" id="account-dropdown">
                    <div class="account-dropdown-header">{{ auth()->user()->email }}</div>
                    <div class="theme-row">
                        <span>Dark mode</span>
                        <button type="button" class="theme-switch" id="theme-switch" aria-label="Toggle dark mode"></button>
                    </div>
                    <a href="{{ route('orders.index') }}">{{ auth()->user()->isAdmin() ? 'All Orders' : 'My Orders' }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-ghost submit-btn-full" style="margin-top:0.6rem;">Logout</button>
                    </form>
                </div>
            </div>
        @else
            <div class="account-wrap nav-end">
                <button type="button" class="account-trigger" id="account-trigger">Sign in / Register</button>
                <div class="account-dropdown" id="account-dropdown">
                    <a href="{{ route('login') }}" class="btn-signal submit-btn-full" style="text-align:center; margin-top:0;">Sign in</a>
                    <p style="font-size:0.85rem; text-align:center; margin:0.8rem 0 0;">
                        New here? <a href="{{ route('register') }}" style="color:var(--signal); font-weight:600; display:inline; padding:0;">Register</a>
                    </p>
                </div>
            </div>
        @endauth
    </div>
</nav>

@if (session('status'))
    <div class="alert">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@yield('content')

<footer>Global DropShip Suite</footer>

<div class="toast" id="toast"><span class="dot"></span><span id="toast-text">Added to cart</span></div>

<script type="module">
    import { initNavProgress, initScrollReveal, initAccountDropdown, initThemeToggle } from "{{ asset('js/app.js') }}";
    initNavProgress();
    initScrollReveal();
    initAccountDropdown();
    initThemeToggle();
</script>
@yield('scripts')
</body>
</html>