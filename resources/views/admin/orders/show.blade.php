@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)

@push('styles')
<style>
    .order-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .info-card { background: #fff; border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.4rem; }
    .info-card-title {
        font-weight: 700; font-size: .82rem;
        text-transform: uppercase; letter-spacing: .06em;
        color: var(--muted); margin-bottom: 1rem;
        padding-bottom: .5rem; border-bottom: 1.5px solid var(--border);
    }
    .info-row { display: flex; gap: .5rem; margin-bottom: .5rem; font-size: .93rem; }
    .info-label { color: var(--muted); min-width: 90px; font-size: .85rem; }
    .info-value { font-weight: 500; }

    /* Order item row */
    .order-item-row {
        display: flex; align-items: center;
        gap: 1rem; padding: .9rem 0;
        border-bottom: 1px solid var(--border);
    }
    .order-item-row:last-child { border-bottom: none; }
    .item-img {
        width: 58px; height: 58px;
        border-radius: 8px; overflow: hidden;
        background: var(--surface); flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .item-img img { width: 100%; height: 100%; object-fit: cover; }
    .item-details { flex: 1; min-width: 0; }
    .item-name { font-weight: 600; font-size: .95rem; margin-bottom: .2rem; }
    .item-meta { font-size: .82rem; color: var(--muted); }
    .item-subtotal { font-weight: 700; font-size: .95rem; white-space: nowrap; }

    /* Totals */
    .totals-row {
        display: flex; justify-content: space-between;
        padding: .5rem 0; font-size: .93rem;
    }
    .totals-row.grand-total {
        font-size: 1.1rem; font-weight: 700;
        border-top: 2px solid var(--border);
        margin-top: .5rem; padding-top: .9rem;
    }

    /* Status timeline */
    .status-timeline {
        display: flex; gap: 0; margin-bottom: 1.5rem;
        background: #fff; border-radius: var(--radius);
        box-shadow: var(--shadow); padding: 1.2rem 1.5rem;
        overflow-x: auto;
    }
    .timeline-step {
        flex: 1; text-align: center;
        position: relative; min-width: 80px;
    }
    .timeline-step::before {
        content: ''; position: absolute;
        top: 16px; left: 50%; right: -50%;
        height: 2px; background: var(--border); z-index: 0;
    }
    .timeline-step:last-child::before { display: none; }
    .step-dot {
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--border); color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto .5rem; font-size: .85rem;
        position: relative; z-index: 1;
        border: 2px solid var(--border);
    }
    .timeline-step.done .step-dot {
        background: var(--success); color: #fff;
        border-color: var(--success);
    }
    .timeline-step.current .step-dot {
        background: var(--brand); color: #fff;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(26,60,94,.15);
    }
    .timeline-step.cancelled .step-dot {
        background: var(--danger); color: #fff;
        border-color: var(--danger);
    }
    .step-label { font-size: .78rem; font-weight: 600; color: var(--muted); }
    .timeline-step.done .step-label,
    .timeline-step.current .step-label { color: var(--text); }

    /* Admin nav */
    .admin-nav { display: flex; gap: .5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); }
    .admin-nav a {
        padding: .65rem 1.2rem; border-radius: 8px 8px 0 0;
        font-weight: 600; font-size: .9rem;
        background: #fff; border: 1.5px solid var(--border);
        border-bottom: none; color: var(--muted);
        margin-bottom: -2px; text-decoration: none;
    }
    .admin-nav a.active { color: var(--brand); border-bottom-color: #fff; }

    .notes-box {
        background: var(--accent-lt); border-left: 3px solid var(--accent);
        padding: .8rem 1rem; border-radius: 0 6px 6px 0;
        font-size: .9rem; margin-top: .5rem;
    }

    @media (max-width: 768px) {
        .order-detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width:900px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.2rem;flex-wrap:wrap">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-sm">← Back to Orders</a>
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:1.4rem;margin:0">
                Order {{ $order->order_number }}
            </h1>
            <p style="color:var(--muted);font-size:.85rem;margin:.2rem 0 0">
                Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                @if($order->paid_at)
                    · Paid {{ $order->paid_at->format('d M Y, h:i A') }}
                @endif
            </p>
        </div>
        <span class="badge {{ $order->status_badge }}" style="margin-left:auto;font-size:.9rem;padding:.4rem 1rem">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    {{-- Admin Nav --}}
    <div class="admin-nav">
        <a href="{{ route('admin.orders.index') }}" class="active">📦 Orders</a>
        <a href="{{ route('admin.products.index') }}">🛍️ Products</a>
        <a href="{{ route('admin.categories.index') }}">🗂️ Categories</a>
    </div>

    {{-- Status Timeline --}}
    @php
        $steps = [
            'pending'    => ['icon' => '🕐', 'label' => 'Pending'],
            'processing' => ['icon' => '⚙️',  'label' => 'Processing'],
            'shipped'    => ['icon' => '🚚', 'label' => 'Shipped'],
            'delivered'  => ['icon' => '✅', 'label' => 'Delivered'],
        ];
        $statusOrder  = array_keys($steps);
        $currentIndex = array_search($order->status, $statusOrder);
    @endphp

    @if($order->status !== 'cancelled')
    <div class="status-timeline">
        @foreach($steps as $key => $step)
            @php
                $stepIndex = array_search($key, $statusOrder);
                $class = $stepIndex < $currentIndex ? 'done'
                       : ($stepIndex === $currentIndex ? 'current' : '');
            @endphp
            <div class="timeline-step {{ $class }}">
                <div class="step-dot">{{ $step['icon'] }}</div>
                <div class="step-label">{{ $step['label'] }}</div>
            </div>
        @endforeach
    </div>
    @else
    <div style="background:#fee2e2;border-left:4px solid var(--danger);padding:.9rem 1.2rem;border-radius:0 8px 8px 0;margin-bottom:1.5rem;font-weight:600;color:#991b1b">
        ❌ This order has been cancelled
    </div>
    @endif

    {{-- Customer & Shipping --}}
<div class="order-detail-grid">
    <div class="info-card">
        <p class="info-card-title">👤 Customer Details</p>
        @php $customer = $order->user?->customer @endphp
        <div class="info-row">
            <span class="info-label">Name</span>
            <span class="info-value">{{ $customer?->name ?? $order->user?->name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value">
                <a href="mailto:{{ $customer?->email ?? $order->user?->email }}" style="color:var(--brand)">
                    {{ $customer?->email ?? $order->user?->email ?? '—' }}
                </a>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value">
                <a href="tel:{{ $customer?->phone ?? $order->user?->phone }}" style="color:var(--brand)">
                    {{ $customer?->phone ?? $order->user?->phone ?? '—' }}
                </a>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Account</span>
            <span class="badge badge-info">Registered</span>
        </div>
    </div>

    <div class="info-card">
        <p class="info-card-title">📍 Shipping Address</p>
        <div class="info-row">
            <span class="info-label">Address</span>
            <span class="info-value">{{ $customer?->shipping_address ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">City</span>
            <span class="info-value">{{ $customer?->shipping_city ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">State</span>
            <span class="info-value">{{ $customer?->shipping_state ?? '—' }}</span>
        </div>
        @if($customer?->shipping_zip)
        <div class="info-row">
            <span class="info-label">ZIP</span>
            <span class="info-value">{{ $customer->shipping_zip }}</span>
        </div>
        @endif
    </div>
</div>

    {{-- Order Notes --}}
    @if($order->notes)
    <div class="info-card" style="margin-bottom:1.5rem">
        <p class="info-card-title">📝 Order Notes</p>
        <div class="notes-box">{{ $order->notes }}</div>
    </div>
    @endif

    {{-- Order Items --}}
    <div class="info-card" style="margin-bottom:1.5rem">
        <p class="info-card-title">🛍️ Order Items ({{ $order->items->count() }})</p>

        @foreach($order->items as $item)
        <div class="order-item-row">
            <div class="item-img">
                @php
                    $images = is_array($item->product_image)
                        ? $item->product_image
                        : json_decode($item->product_image, true);
                    $firstImg = is_array($images) ? ($images[0] ?? null) : $item->product_image;
                @endphp
                @if($firstImg)
                    <img src="{{ asset('storage/' . $firstImg) }}" alt="{{ $item->product_name }}">
                @else
                    🛍️
                @endif
            </div>
            <div class="item-details">
                <div class="item-name">{{ $item->product_name }}</div>
                <div class="item-meta">
                    Unit price: ₦{{ number_format($item->unit_price, 2) }}
                    @if($item->product)
                        ·
                        <a href="{{ route('products.show', $item->product->slug) }}"
                           target="_blank" style="color:var(--brand)">View product ↗</a>
                    @else
                        · <span style="color:var(--danger)">Product deleted</span>
                    @endif
                </div>
            </div>
            <div style="text-align:right">
                <div class="item-subtotal">₦{{ number_format($item->subtotal, 2) }}</div>
                <div style="font-size:.82rem;color:var(--muted)">Qty: {{ $item->quantity }}</div>
            </div>
        </div>
        @endforeach

        {{-- Totals --}}
        <div style="margin-top:1rem;padding-top:.5rem">
            <div class="totals-row">
                <span style="color:var(--muted)">Subtotal</span>
                <span>₦{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="totals-row">
                <span style="color:var(--muted)">VAT (7.5%)</span>
                <span>₦{{ number_format($order->tax, 2) }}</span>
            </div>
            <div class="totals-row">
                <span style="color:var(--muted)">Shipping</span>
                <span>
                    {{ $order->shipping == 0 ? '🎉 Free' : '₦' . number_format($order->shipping, 2) }}
                </span>
            </div>
            <div class="totals-row grand-total">
                <span>Total</span>
                <span>₦{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Update Status --}}
    <div class="info-card">
        <p class="info-card-title">🔄 Update Order Status</p>
        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}"
              style="display:flex;gap:.8rem;align-items:center;flex-wrap:wrap">
            @csrf @method('PATCH')
            <select name="status" class="form-control" style="width:auto;min-width:180px">
                @foreach(\App\Models\Order::statuses() as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Update Status</button>
            @if(session('success'))
                <span style="color:var(--success);font-size:.88rem;font-weight:600">
                    ✓ {{ session('success') }}
                </span>
            @endif
        </form>
    </div>

</div>
@endsection
