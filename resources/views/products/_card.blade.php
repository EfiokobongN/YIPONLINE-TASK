@php
    $images   = is_array($product->image)
                ? $product->image
                : json_decode($product->image, true);
    $firstImg = is_array($images) ? ($images[0] ?? null) : null;
@endphp

<div class="product-card">
    <div class="product-img">
        @if($firstImg)
            <img src="{{ asset('storage/' . $firstImg) }}"
                 alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="placeholder">🛍️</div>
        @endif
        @if($product->discount_percent)
            <span class="product-badge">-{{ $product->discount_percent }}%</span>
        @endif
        @if($product->is_featured)
            <span class="featured-badge">Featured</span>
        @endif
    </div>
    <div class="product-body">
        {{-- Category via relationship --}}
        <span class="product-category">{{ $product->category?->name ?? '—' }}</span>

        <h3 class="product-name">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        <div class="product-pricing">
            <span class="product-price">₦{{ number_format($product->price, 2) }}</span>
            @if($product->compare_price)
                <span class="product-compare">₦{{ number_format($product->compare_price, 2) }}</span>
            @endif
        </div>
        <div class="add-btn">
            @if($product->isInStock())
                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-accent btn-block btn-sm">Add to Cart</button>
                </form>
            @else
                <span class="badge badge-danger">Out of Stock</span>
            @endif
        </div>
    </div>
</div>
