@extends('layouts.app')
@section('title', 'Checkout')

@push('styles')
<style>
    .checkout-layout { display: grid; grid-template-columns: 1fr 360px; gap: 2rem; align-items: start; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .section-title {
        font-size: 1rem; font-weight: 700;
        border-bottom: 2px solid var(--brand);
        padding-bottom: .4rem; margin-bottom: 1.2rem;
        color: var(--brand);
    }
    .order-review-item {
        display: flex; gap: 1rem; margin-bottom: 1rem;
        padding-bottom: 1rem; border-bottom: 1px solid var(--border);
    }
    .review-img {
        width: 56px; height: 56px; border-radius: 6px;
        background: var(--surface); display: flex;
        align-items: center; justify-content: center;
        font-size: 1.5rem; overflow: hidden; flex-shrink: 0;
    }
    .review-img img { width: 100%; height: 100%; object-fit: cover; }
    .review-name { font-size: .9rem; font-weight: 600; margin-bottom: .2rem; }
    .review-qty { font-size: .82rem; color: var(--muted); }
    .review-price { margin-left: auto; font-weight: 600; font-size: .95rem; white-space: nowrap; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: .7rem; font-size: .93rem; }
    .summary-row.total { font-size: 1.1rem; font-weight: 700; padding-top: .8rem; border-top: 2px solid var(--border); }
    .payment-mock {
        background: var(--accent-lt); border: 1.5px dashed var(--accent);
        border-radius: 8px; padding: 1rem;
        font-size: .88rem; color: #92400e; margin-bottom: 1.2rem;
    }
    .info-note {
        background: #eff6ff; border-left: 3px solid #3b82f6;
        padding: .7rem 1rem; border-radius: 0 6px 6px 0;
        font-size: .85rem; color: #1e40af; margin-bottom: 1.2rem;
    }
    @media (max-width: 768px) {
        .checkout-layout { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:1.5rem">Checkout</h1>

    <form method="POST" action="{{ route('checkout.place-order') }}">
        @csrf
        <div class="checkout-layout">

            {{-- Left: Shipping form --}}
            <div class="card card-body">

                {{-- Personal Info --}}
                <p class="section-title">Personal Information</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                               value="{{ old('name', $customer->name ?? $user->name) }}">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                               value="{{ old('email', $customer->email ?? $user->email ) }}">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="{{ old('phone', $customer->phone ?? '') }}">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Delivery Address --}}
                <p class="section-title" style="margin-top:1.2rem">Delivery Address</p>

                <div class="form-group">
                    <label for="address">Street Address *</label>
                    <input type="text" name="address" id="address" class="form-control"
                           value="{{ old('address', $customer->shipping_address ?? '') }}"
                           required placeholder="e.g. 12 Adeola Odeku Street">
                    @error('address') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" name="city" id="city" class="form-control"
                               value="{{ old('city', $customer->shipping_city ?? '') }}" required>
                        @error('city') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="state">State *</label>
                        <input type="text" name="state" id="state" class="form-control"
                               value="{{ old('state', $customer->shipping_state ?? '') }}" required>
                        @error('state') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="zip">ZIP / Postal Code</label>
                    <input type="text" name="zip" id="zip" class="form-control"
                           value="{{ old('zip', $customer->shipping_zip ?? '') }}">
                </div>

                <div class="form-group">
                    <label for="notes">Order Notes (optional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"
                              placeholder="Any special delivery instructions?">{{ old('notes') }}</textarea>
                </div>

                {{-- Payment  --}}
                <p class="section-title" style="margin-top:1rem">Payment</p>

                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" class="form-control" value="4242 4242 4242 4242" disabled>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Expiry</label>
                        <input type="text" class="form-control" value="12/28" disabled>
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="text" class="form-control" value="123" disabled>
                    </div>
                </div>

                <button type="submit" class="btn btn-accent btn-block"
                        style="font-size:1.05rem;padding:.9rem;margin-top:.5rem">
                    🔒 Place Order — ₦{{ number_format($total, 2) }}
                </button>
            </div>

            {{-- Right: Order summary --}}
            <div>
                <div class="card card-body" style="position:sticky;top:80px">
                    <p class="section-title">Order Review</p>

                    @foreach($cart as $item)
                    <div class="order-review-item">
                        <div class="review-img">
                            @php
                                $images = is_array($item['image'])
                                    ? $item['image']
                                    : json_decode($item['image'], true);
                                $firstImage = is_array($images) ? ($images[0] ?? null) : $item['image'];
                            @endphp
                            @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $item['name'] }}">
                            @else
                                🛍️
                            @endif
                        </div>
                        <div style="flex:1;min-width:0">
                            <div class="review-name">{{ Str::limit($item['name'], 30) }}</div>
                            <div class="review-qty">Qty: {{ $item['quantity'] }}</div>
                        </div>
                        <div class="review-price">
                            ₦{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>
                    </div>
                    @endforeach

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
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>₦{{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('cart.index') }}"
                       style="display:block;text-align:center;margin-top:.8rem;font-size:.85rem;color:var(--muted)">
                        ← Edit Cart
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
