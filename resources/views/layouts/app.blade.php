<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'YIPShop') — Premium Online Store</title>
    <meta name="description" content="@yield('meta_description', 'Shop the best products at YIPShop')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand:      #1a3c5e;
            --accent:     #e8a020;
            --accent-lt:  #fdf3e3;
            --surface:    #f8f9fb;
            --border:     #e2e8f0;
            --text:       #1e293b;
            --muted:      #64748b;
            --danger:     #dc2626;
            --success:    #16a34a;
            --radius:     10px;
            --shadow:     0 2px 16px rgba(0,0,0,.08);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--text);
            line-height: 1.6;
        }

        a { color: inherit; text-decoration: none; }

        /* ── Navbar ── */
        .navbar {
            background: var(--brand);
            padding: .85rem 0;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,.18);
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; gap: 1.5rem;
        }
        .nav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem; color: #fff; font-weight: 700;
            letter-spacing: -.5px; white-space: nowrap;
        }
        .nav-brand span { color: var(--accent); }
        .nav-search {
            flex: 1; display: flex; gap: .5rem;
        }
        .nav-search input {
            flex: 1; padding: .5rem 1rem;
            border: none; border-radius: 6px;
            font-size: .95rem;
        }
        .nav-search button {
            background: var(--accent); color: #fff;
            border: none; padding: .5rem 1rem;
            border-radius: 6px; cursor: pointer; font-weight: 600;
        }
        .nav-links {
            display: flex; align-items: center; gap: 1rem;
            list-style: none;
        }
        .nav-links a {
            color: rgba(255,255,255,.85); font-size: .9rem;
            padding: .35rem .7rem; border-radius: 5px;
            transition: background .15s;
        }
        .nav-links a:hover { background: rgba(255,255,255,.12); color: #fff; }
        .nav-cart {
            background: var(--accent); color: #fff !important;
            padding: .4rem .9rem !important; border-radius: 6px !important;
            font-weight: 600 !important;
        }
        .cart-count {
            background: #fff; color: var(--brand);
            border-radius: 50%; width: 18px; height: 18px;
            font-size: .72rem; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            margin-left: .3rem;
        }

        /* ── Hamburger ── */
        .hamburger {
            display: none; background: none; border: none;
            color: #fff; font-size: 1.5rem; cursor: pointer; margin-left: auto;
        }

        /* ── Alerts ── */
        .alert {
            max-width: 1200px; margin: 1rem auto; padding: .85rem 1.5rem;
            border-radius: var(--radius); font-weight: 500;
        }
        .alert-success { background: #dcfce7; color: #166534; border-left: 4px solid var(--success); }
        .alert-error   { background: #fee2e2; color: #991b1b; border-left: 4px solid var(--danger); }

        /* ── Page wrapper ── */
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem 1.5rem; }

        /* ── Cards ── */
        .card {
            background: #fff; border-radius: var(--radius);
            box-shadow: var(--shadow); overflow: hidden;
        }
        .card-body { padding: 1.5rem; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
            padding: .6rem 1.4rem; border-radius: var(--radius);
            font-size: .95rem; font-weight: 600;
            border: none; cursor: pointer; transition: opacity .15s, transform .1s;
        }
        .btn:hover { opacity: .88; transform: translateY(-1px); }
        .btn-primary   { background: var(--brand); color: #fff; }
        .btn-accent    { background: var(--accent); color: #fff; }
        .btn-outline   { background: transparent; border: 2px solid var(--brand); color: var(--brand); }
        .btn-danger    { background: var(--danger); color: #fff; }
        .btn-sm        { padding: .38rem .85rem; font-size: .85rem; }
        .btn-block     { width: 100%; }

        /* ── Badges ── */
        .badge { display: inline-block; padding: .25rem .65rem; border-radius: 50px; font-size: .78rem; font-weight: 600; }
        .badge-warning  { background: #fef3c7; color: #92400e; }
        .badge-info     { background: #dbeafe; color: #1e40af; }
        .badge-primary  { background: #ede9fe; color: #5b21b6; }
        .badge-success  { background: #dcfce7; color: #166534; }
        .badge-danger   { background: #fee2e2; color: #991b1b; }
        .badge-secondary{ background: #f1f5f9; color: var(--muted); }

        /* ── Forms ── */
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: .4rem; font-size: .9rem; }
        .form-control {
            width: 100%; padding: .62rem .9rem;
            border: 1.5px solid var(--border); border-radius: 8px;
            font-size: .95rem; background: #fff;
            transition: border-color .15s;
        }
        .form-control:focus { outline: none; border-color: var(--brand); }
        .form-error { color: var(--danger); font-size: .83rem; margin-top: .25rem; }

        /* ── Footer ── */
        .footer {
            background: var(--brand); color: rgba(255,255,255,.8);
            text-align: center; padding: 2.5rem 1.5rem;
            margin-top: 4rem;
        }
        .footer a { color: var(--accent); }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .hamburger { display: block; }
            .nav-search { display: none; }
            .nav-links { display: none; flex-direction: column; gap: .5rem; }
            .nav-links.open {
                display: flex; position: absolute; top: 60px; left: 0; right: 0;
                background: var(--brand); padding: 1rem 1.5rem;
            }
            .nav-inner { flex-wrap: wrap; position: relative; }
            .nav-brand { flex: 1; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <div class="nav-inner">
            <a href="{{ route('home') }}" class="nav-brand">YIP<span>Shop</span></a>

            {{-- Hide search bar for admin --}}
            @if(!auth()->check() || !auth()->user()->isAdmin())
                <form class="nav-search" action="{{ route('products.search') }}" method="GET">
                    <input type="text" name="q" placeholder="Search products…"
                           value="{{ request('q') }}">
                    <button type="submit">Search</button>
                </form>
            @else
                {{-- Admin brand/title instead of search --}}
                <span style="color:rgba(255,255,255,.6);font-size:.9rem;flex:1">
                    Admin Panel
                </span>
            @endif

            <button class="hamburger"
                    onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>

            <ul class="nav-links">
                @auth
                    @if(auth()->user()->isAdmin())
                    <li><a href="{{ route('home') }}">Shop</a></li>
                        <li><a href="{{ route('admin.orders.index') }}">📦 Orders</a></li>
                        <li><a href="{{ route('admin.products.index') }}">🛍️ Products</a></li>
                        <li><a href="{{ route('admin.categories.index') }}">🗂️ Categories</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                                @csrf
                                <button type="submit" style="background:none;border:none;color:rgba(255,255,255,.85);cursor:pointer;font-size:.9rem">
                                    Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('home') }}">Shop</a></li>
                        <li><a href="{{ route('orders.index') }}">My Orders</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                                @csrf
                                <button type="submit" style="background:none;border:none;color:rgba(255,255,255,.85);cursor:pointer;font-size:.9rem">
                                    Logout
                                </button>
                            </form>
                        </li>
                        <li>
                            <a href="{{ route('cart.index') }}" class="nav-cart">
                                🛒 Cart
                                @php $cartCount = collect(session('cart', []))->sum('quantity') @endphp
                                @if($cartCount > 0)
                                    <span class="cart-count">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @else
                    <li><a href="{{ route('home') }}">Shop</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                    <li>
                        <a href="{{ route('cart.index') }}" class="nav-cart">
                            🛒 Cart
                            @php $cartCount = collect(session('cart', []))->sum('quantity') @endphp
                            @if($cartCount > 0)
                                <span class="cart-count">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>

@if(session('success'))
    <div class="alert alert-success container">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error container">{{ session('error') }}</div>
@endif

@yield('content')

<footer class="footer">
    <p>© {{ date('Y') }} <strong>YIPShop</strong> — Built with Laravel for <a href="#">YIPONLINE</a></p>
    <p style="font-size:.85rem;margin-top:.5rem;">Case Study Submission</p>
</footer>

@stack('scripts')
</body>
</html>
