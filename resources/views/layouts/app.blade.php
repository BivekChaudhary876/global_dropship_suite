<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Global DropShip Suite')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f6f7f9; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background: #1f2937; color: #fff; padding: 0.8rem 1.5rem; }
        .navbar a { color: #d1d5db; text-decoration: none; margin-left: 1rem; }
        .navbar a:hover { color: #fff; }
        .container { max-width: 1000px; margin: 1.5rem auto; padding: 0 1rem; }
        .alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        label { display: block; font-weight: 600; margin-top: 0.75rem; }
        input { width: 100%; padding: 0.5rem; margin-top: 0.25rem; border: 1px solid #d1d5db; border-radius: 6px; }
        .btn { display: inline-block; background: #2563eb; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; text-decoration: none; }
        .star-rating { font-size: 1.1rem; white-space: nowrap; }
        .star { display: inline-block; }
        .star-full { color: #f59e0b; }
        .star-empty { color: #d1d5db; }
        .star-half { position: relative; display: inline-block; width: 1em; }
        .star-half .star-empty { position: absolute; left: 0; }
        .star-half .star-half-inner { position: absolute; left: 0; width: 50%; overflow: hidden; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('products.index') }}" style="color:#fff;font-weight:bold;">Global DropShip Suite</a>
        <div>
            @auth
                <a href="{{ route('cart.index') }}">Cart (<span id="cart-count">{{ array_sum(session('cart', [])) }}</span>)</a>
                <a href="{{ route('orders.index') }}">My Orders</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.tags.index') }}">Tags</a>
                @endif
                <span style="color:#9ca3af;">Signed in as {{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:#d1d5db;cursor:pointer;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </nav>
    <main class="container">
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
    </main>
    <script>
        function addToCartAjax(url, quantity, buttonEl) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            buttonEl.disabled = true;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity: quantity || 1 }),
            })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count;
                    const msg = document.getElementById('cart-flash');
                    if (msg) {
                        msg.textContent = data.message;
                        msg.style.display = 'block';
                        setTimeout(() => { msg.style.display = 'none'; }, 2500);
                    }
                })
                .finally(() => { buttonEl.disabled = false; });
        }
    </script>
</body>
</html>