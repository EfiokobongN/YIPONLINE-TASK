@extends('layouts.app')
@section('title', $product->name)

@push('styles')
<style>
    .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start; }
    .product-gallery { position: sticky; top: 80px; }
    .main-image {
        border-radius: var(--radius); overflow: hidden;
        aspect-ratio: 1; background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        font-size: 6rem;
    }
    .main-image img { width: 100%; height: 100%; object-fit: cover; }
    .thumb-grid { display: flex; gap: .5rem; margin-top: .6rem; flex-wrap: wrap; }
    .thumb {
        width: 70px; height: 70px; border-radius: 6px;
        overflow: hidden; cursor: pointer;
        border: 2px solid transparent; transition: border-color .15s;
    }
    .thumb:hover, .thumb.active { border-color: var(--brand); }
    .thumb img { width: 100%; height: 100%; object-fit: cover; }
    .product-info-panel { padding: 0; }
    .product-info-panel .category {
        color: var(--muted); font-size: .85rem;
        text-transform: uppercase; letter-spacing: .05em;
    }
    .product-info-panel h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.5rem, 3vw, 2rem);
        line-height: 1.2; margin: .5rem 0 1rem;
    }
    .price-block { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
    .price-main { font-size: 1.8rem; font-weight: 700; color: var(--brand); }
    .price-compare { font-size: 1.1rem; color: var(--muted); text-decoration: line-through; }
    .discount-tag {
        background: var(--danger); color: #fff;
        padding: .2rem .6rem; border-radius: 4px;
        font-size: .82rem; font-weight: 700;
    }
    .product-desc { color: var(--muted); line-height: 1.8; margin-bottom: 1.5rem; }
    .stock-indicator {
        display: flex; align-items: center; gap: .4rem;
        margin-bottom: 1.5rem; font-size: .9rem; font-weight: 500;
    }
    .stock-dot { width: 10px; height: 10px; border-radius: 50%; }
    .in-stock .stock-dot  { background: var(--success); }
    .out-stock .stock-dot { background: var(--danger); }
    .qty-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
    .qty-row label { font-weight: 600; }
    .qty-input { width: 80px; padding: .5rem; border: 1.5px solid var(--border); border-radius: 8px; text-align: center; font-size: 1rem; }
    .related-section { margin-top: 4rem; }
    .related-section h2 { font-family: 'Playfair Display', serif; font-size: 1.4rem; margin-bottom: 1.5rem; }
    .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.2rem; }
    .breadcrumb { color: var(--muted); font-size: .88rem; margin-bottom: 1.5rem; }
    .breadcrumb a { color: var(--brand); }
    @media (max-width: 768px) {
        .product-detail { grid-template-columns: 1fr; }
        .product-gallery { position: static; }
    }
</style>
@endpush

@section('content')
<div class="container">

    {{-- Breadcrumb - now uses category relationship --}}
    <p class="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        @if($product->category)
            ›
            <a href="{{ route('home', ['category_id' => $product->category_id]) }}">
                {{ $product->category->name }}
            </a>
        @endif
        › {{ $product->name }}
    </p>

    <div class="product-detail">

        {{-- Gallery - handles JSON array of images --}}
        <div class="product-gallery">
            @php
                $images = is_array($product->image)
                    ? $product->image
                    : (json_decode($product->image, true) ?? []);
                $firstImage = $images[0] ?? null;
            @endphp

            <div class="main-image card" id="mainImage">
                @if($firstImage)
                    <img src="{{ asset('storage/' . $firstImage) }}"
                         alt="{{ $product->name }}" id="mainImg">
                @else
                    🛍️
                @endif
            </div>

            {{-- Thumbnails if multiple images --}}
            @if(count($images) > 1)
            <div class="thumb-grid">
                @foreach($images as $i => $img)
                    <div class="thumb {{ $i === 0 ? 'active' : '' }}"
                         onclick="switchImage('{{ asset('storage/' . $img) }}', this)">
                        <img src="{{ asset('storage/' . $img) }}" alt="Image {{ $i + 1 }}">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="product-info-panel">

            {{-- Category name via relationship --}}
            <p class="category">
                {{ $product->category?->name ?? 'Uncategorized' }}
            </p>

            <h1>{{ $product->name }}</h1>

            <div class="price-block">
                <span class="price-main">₦{{ number_format($product->price, 2) }}</span>
                @if($product->compare_price)
                    <span class="price-compare">₦{{ number_format($product->compare_price, 2) }}</span>
                    @if($product->discount_percent)
                        <span class="discount-tag">{{ $product->discount_percent }}% OFF</span>
                    @endif
                @endif
            </div>

            <div class="stock-indicator {{ $product->isInStock() ? 'in-stock' : 'out-stock' }}">
                <span class="stock-dot"></span>
                @if($product->isInStock())
                    In Stock ({{ $product->stock }} available)
                @else
                    Out of Stock
                @endif
            </div>

            <p class="product-desc">{{ $product->description }}</p>

            @if($product->isInStock())
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="qty-row">
                        <label for="qty">Quantity:</label>
                        <input type="number" id="qty" name="quantity"
                               value="1" min="1" max="{{ $product->stock }}"
                               class="qty-input">
                    </div>
                    <button type="submit" class="btn btn-accent btn-block"
                            style="font-size:1rem;padding:.85rem">
                        🛒 Add to Cart
                    </button>
                </form>
                <a href="{{ route('cart.index') }}" class="btn btn-outline btn-block"
                   style="margin-top:.8rem">
                    View Cart
                </a>
            @else
                <div class="btn btn-outline btn-block"
                     style="opacity:.5;cursor:not-allowed">Out of Stock</div>
            @endif
        </div>
    </div>

    {{-- Related products --}}
    @if($related->isNotEmpty())
    <div class="related-section">
        <h2>You might also like</h2>
        <div class="related-grid">
            @foreach($related as $product)
                @include('products._card')
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function switchImage(src, thumbEl) {
        document.getElementById('mainImg').src = src;
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        thumbEl.classList.add('active');
    }
</script>
@endpush
