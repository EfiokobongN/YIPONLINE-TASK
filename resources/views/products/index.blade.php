@extends('layouts.app')
@section('title', 'Shop All Products')

@push('styles')
<style>
    .hero {
        background: linear-gradient(135deg, var(--brand) 0%, #2d6a9f 100%);
        color: #fff; padding: 3.5rem 1.5rem; text-align: center;
    }
    .hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 5vw, 3rem); margin-bottom: .75rem;
    }
    .hero p { font-size: 1.1rem; opacity: .85; }

    .shop-layout { display: grid; grid-template-columns: 220px 1fr; gap: 2rem; }

    /* Sidebar */
    .sidebar-section {
        background: #fff; border-radius: var(--radius);
        padding: 1.2rem; margin-bottom: 1rem; box-shadow: var(--shadow);
    }
    .sidebar-section h3 {
        font-size: .85rem; text-transform: uppercase;
        letter-spacing: .05em; color: var(--muted); margin-bottom: .8rem;
    }
    .category-link {
        display: flex; align-items: center; gap: .7rem;
        padding: .5rem .6rem; border-radius: 8px;
        font-size: .93rem; color: var(--text);
        transition: background .15s; margin-bottom: .3rem;
    }
    .category-link:hover,
    .category-link.active {
        background: var(--accent-lt); color: var(--brand); font-weight: 600;
    }
    .cat-icon {
        width: 34px; height: 34px; border-radius: 6px;
        overflow: hidden; flex-shrink: 0; background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; border: 1.5px solid var(--border);
    }
    .cat-icon img { width: 100%; height: 100%; object-fit: cover; }
    .category-link.active .cat-icon { border-color: var(--brand); }
    .sort-select {
        width: 100%; padding: .5rem;
        border: 1.5px solid var(--border); border-radius: 6px;
    }

    /* Product grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.4rem;
    }
    .product-card {
        background: #fff; border-radius: var(--radius);
        box-shadow: var(--shadow); overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        display: flex; flex-direction: column;
    }
    .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,.12); }
    .product-img {
        aspect-ratio: 4/3; overflow: hidden;
        background: var(--surface); position: relative;
    }
    .product-img img { width: 100%; height: 100%; object-fit: cover; }
    .product-img .placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; color: var(--border);
    }
    .product-badge {
        position: absolute; top: .6rem; left: .6rem;
        background: var(--danger); color: #fff;
        font-size: .72rem; font-weight: 700;
        padding: .2rem .5rem; border-radius: 4px;
    }
    .featured-badge {
        position: absolute; top: .6rem; right: .6rem;
        background: var(--accent); color: #fff;
        font-size: .72rem; font-weight: 700;
        padding: .2rem .5rem; border-radius: 4px;
    }
    .product-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
    .product-category { font-size: .75rem; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; }
    .product-name { font-weight: 600; margin: .3rem 0 .5rem; font-size: .97rem; line-height: 1.3; }
    .product-name a:hover { color: var(--brand); }
    .product-pricing { display: flex; align-items: center; gap: .5rem; margin-bottom: .8rem; }
    .product-price { font-size: 1.05rem; font-weight: 700; color: var(--brand); }
    .product-compare { font-size: .85rem; color: var(--muted); text-decoration: line-through; }
    .add-btn { margin-top: auto; }

    /* Featured strip */
    .featured-strip { margin-bottom: 2rem; }
    .featured-strip h2 { font-family: 'Playfair Display', serif; font-size: 1.4rem; margin-bottom: 1rem; }

    /* Mobile filter toggle button */
    .filter-toggle-btn {
        display: none;
        width: 100%; padding: .75rem 1rem;
        background: var(--brand); color: #fff;
        border: none; border-radius: var(--radius);
        font-size: .95rem; font-weight: 600;
        cursor: pointer; margin-bottom: 1rem;
        align-items: center; justify-content: center; gap: .5rem;
    }

    /* Mobile sidebar drawer */
    .sidebar-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 200;
    }
    .sidebar-overlay.open { display: block; }

    /* Pagination */
    .pagination { display: flex; justify-content: center; gap: .4rem; margin-top: 2rem; flex-wrap: wrap; }
    .pagination a, .pagination span {
        padding: .5rem .85rem; border-radius: 6px;
        border: 1.5px solid var(--border); background: #fff; font-size: .9rem;
    }
    .pagination .active { background: var(--brand); color: #fff; border-color: var(--brand); }

    /* ── Desktop ── */
    @media (min-width: 769px) {
        .filter-toggle-btn { display: none !important; }
        .sidebar { display: block; }
    }

    /* ── Mobile ── */
    @media (max-width: 768px) {
        .shop-layout { grid-template-columns: 1fr; }

        .filter-toggle-btn { display: flex; }

        /* Sidebar becomes a slide-in drawer */
        .sidebar {
            display: block;
            position: fixed;
            top: 0; left: -280px;
            width: 280px; height: 100vh;
            background: var(--surface);
            z-index: 201;
            overflow-y: auto;
            padding: 1rem;
            transition: left .3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,.15);
        }
        .sidebar.open { left: 0; }

        .sidebar-close {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1rem; padding-bottom: .8rem;
            border-bottom: 1.5px solid var(--border);
        }
        .sidebar-close span { font-weight: 700; font-size: 1rem; }
        .sidebar-close button {
            background: none; border: none;
            font-size: 1.4rem; cursor: pointer; color: var(--muted);
        }
    }

    /* Hide close button on desktop */
    @media (min-width: 769px) {
        .sidebar-close { display: none; }
    }
</style>
@endpush

@section('content')

<div class="hero">
    <h1>Shop Everything</h1>
    <p>Discover quality products at the best prices</p>
</div>

<div class="container">

    @if(isset($featured) && $featured->isNotEmpty() && !isset($searchQuery))
    <div class="featured-strip">
        <h2>⭐ Featured Products</h2>
        <div class="product-grid">
            @foreach($featured as $p)
                @include('products._card', ['product' => $p])
            @endforeach
        </div>
    </div>
    <hr style="border:none;border-top:1.5px solid var(--border);margin:2rem 0">
    @endif

    {{-- Mobile filter toggle button --}}
    <button class="filter-toggle-btn" onclick="openSidebar()">
        🔽 Filter & Categories
    </button>

    {{-- Overlay (closes sidebar on tap) --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="shop-layout">

        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">

            {{-- Mobile close header --}}
            <div class="sidebar-close">
                <span>Filter & Categories</span>
                <button onclick="closeSidebar()">×</button>
            </div>

            <div class="sidebar-section">
                <h3>Category</h3>
                <a href="{{ route('home') }}"
                   class="category-link {{ !request('category_id') ? 'active' : '' }}">
                    <div class="cat-icon">🛍️</div>
                    <span>All Products</span>
                </a>
                @foreach($categories ?? [] as $cat)
                    <a href="{{ route('home', ['category_id' => $cat->id]) }}"
                       class="category-link {{ request('category_id') == $cat->id ? 'active' : '' }}">
                        <div class="cat-icon">
                            @if($cat->image)
                                <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}">
                            @else
                                🗂️
                            @endif
                        </div>
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>

            <div class="sidebar-section">
                <h3>Sort By</h3>
                <form method="GET">
                    @if(request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="">Default</option>
                        <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Price: Low → High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                        <option value="newest"     {{ request('sort') === 'newest'     ? 'selected' : '' }}>Newest</option>
                    </select>
                </form>
            </div>
        </aside>

        {{-- Product grid --}}
        <main>
            @if(isset($searchQuery))
                <p style="margin-bottom:1rem;color:var(--muted)">
                    Results for "<strong>{{ $searchQuery }}</strong>" — {{ $products->total() }} found
                </p>
            @endif

            @if($products->isEmpty())
                <div class="card card-body" style="text-align:center;padding:3rem">
                    <p style="font-size:1.1rem;color:var(--muted)">No products found.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary"
                       style="margin-top:1rem;display:inline-block">Browse All</a>
                </div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        @include('products._card')
                    @endforeach
                </div>
                <div class="pagination">
                    {{ $products->links() }}
                </div>
            @endif
        </main>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebarOverlay').classList.add('open');
        document.body.style.overflow = 'hidden'; // prevent background scroll
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
</script>
@endpush
