@extends('layouts.app')
@section('title', 'Your Cart')

@push('styles')
<style>
    .cart-layout { display: grid; grid-template-columns: 1fr 340px; gap: 2rem; align-items: start; }
    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table th {
        text-align: left; padding: .85rem 1rem;
        border-bottom: 2px solid var(--border);
        font-size: .85rem; text-transform: uppercase;
        letter-spacing: .05em; color: var(--muted);
    }
    .cart-table td { padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .cart-item-img {
        width: 70px; height: 70px;
        border-radius: 8px; overflow: hidden;
        background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
    }
    .cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
    .item-info { display: flex; align-items: center; gap: 1rem; }
    .item-name { font-weight: 600; margin-bottom: .2rem; }
    .item-name a:hover { color: var(--brand); }
    .qty-form { display: flex; align-items: center; gap: .4rem; }
    .qty-input-sm {
        width: 65px; padding: .4rem;
        border: 1.5px solid var(--border); border-radius: 6px;
        text-align: center;
    }
    .remove-btn { color: var(--danger); background: none; border: none; cursor: pointer; font-size: .85rem; padding: .3rem .5rem; border-radius: 4px; }
    .remove-btn:hover { background: #fee2e2; }

    /* Summary */
    .order-summary { background: #fff; border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow); position: sticky; top: 80px; }
    .order-summary h2 { font-size: 1.1rem; font-weight: 700; margin-bottom: 1.2rem; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: .7rem; font-size: .93rem; }
    .summary-row.total { font-size: 1.1rem; font-weight: 700; padding-top: .8rem; border-top: 2px solid var(--border); }
    .empty-cart { text-align: center; padding: 4rem 2rem; }
    .empty-cart p { font-size: 1.1rem; color: var(--muted); margin-bottom: 1.5rem; }

    @media (max-width: 768px) {
        .cart-layout { grid-template-columns: 1fr; }
        .order-summary { position: static; }
        .cart-table th:nth-child(3), .cart-table td:nth-child(3) { display: none; }
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:1.5rem">Your Cart</h1>

    @if(empty($cart))
        <div class="card empty-cart">
            <div style="font-size:4rem;margin-bottom:1rem">🛒</div>
            <p>Your cart is empty!</p>
            <a href="{{ route('home') }}" class="btn btn-accent">Start Shopping</a>
        </div>
    @else
        <div class="cart-layout">

            {{-- Items --}}
            <div class="card">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $id => $item)
<tr>
    <td>
        <div class="item-info">
            <div class="cart-item-img">
                @php
                    $images   = is_array($item['image'])
                                ? $item['image']
                                : json_decode($item['image'], true);
                    $firstImg = is_array($images) ? ($images[0] ?? null) : null;
                @endphp
                @if($firstImg)
                    <img src="{{ asset('storage/' . $firstImg) }}" alt="{{ $item['name'] }}">
                @else
                    🛍️
                @endif
            </div>
            <div>
                <div class="item-name">
                    <a href="{{ route('products.show', $item['slug']) }}">{{ $item['name'] }}</a>
                </div>
            </div>
        </div>
    </td>

                            <td>₦{{ number_format($item['price'], 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $id) }}" class="qty-form">
                                    @csrf @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                           min="1" max="{{ $item['stock'] }}" class="qty-input-sm">
                                    <button type="submit" class="btn btn-sm btn-outline">Update</button>
                                </form>
                            </td>
                            <td><strong>₦{{ number_format($item['price'] * $item['quantity'], 2) }}</strong></td>
                            <td>
                                <form method="POST" action="{{ route('cart.remove', $id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="remove-btn">✕ Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding:1rem;display:flex;justify-content:space-between;align-items:center">
                    <a href="{{ route('home') }}" class="btn btn-outline btn-sm">← Continue Shopping</a>
                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Clear entire cart?')">Clear Cart</button>
                    </form>
                </div>
            </div>

            {{-- Summary --}}
            <div class="order-summary">
                <h2>Order Summary</h2>
                @php
                    $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
                    $tax      = round($subtotal * 0.075, 2);
                    $shipping = $subtotal >= 50000 ? 0 : 1500;
                    $total    = $subtotal + $tax + $shipping;
                @endphp
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₦{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>VAT (7.5%)</span>
                    <span>₦{{ number_format($tax, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>{{ $shipping === 0 ? '🎉 Free' : '₦' . number_format($shipping, 2) }}</span>
                </div>
                @if($shipping > 0)
                    <p style="font-size:.8rem;color:var(--muted);margin-bottom:.8rem">
                        Free shipping on orders over ₦50,000
                    </p>
                @endif
                <div class="summary-row total">
                    <span>Total</span>
                    <span>₦{{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-accent btn-block" style="margin-top:1.2rem;font-size:1rem">
                    Proceed to Checkout →
                </a>
            </div>

        </div>
    @endif
</div>
@endsection
