@extends('layouts.app')
@section('title', 'Order Confirmed!')

@section('content')
<div class="container" style="max-width:680px">
    <div class="card card-body" style="text-align:center;padding:3rem 2rem">
        <div style="font-size:4rem;margin-bottom:1rem">✅</div>
        <h1 style="font-family:'Playfair Display',serif;color:var(--brand)">Order Confirmed!</h1>
        <p style="color:var(--muted);margin:.8rem 0 1.5rem">
            Thank you for your order. We'll send a confirmation to
            <strong>{{ $order->shipping_email }}</strong>
        </p>

        <div style="background:var(--surface);border-radius:var(--radius);padding:1.2rem;text-align:left;margin-bottom:1.5rem">
            <p style="font-size:.85rem;color:var(--muted);margin-bottom:.3rem">Order Number</p>
            <p style="font-weight:700;font-size:1.2rem;color:var(--brand)">{{ $order->order_number }}</p>
        </div>

        <div style="text-align:left">
            <p style="font-weight:600;margin-bottom:.8rem">Order Summary</p>
            @foreach($order->items as $item)
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border)">
                <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                <span>₦{{ number_format($item->subtotal, 2) }}</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;padding:.8rem 0;font-weight:700;font-size:1.05rem">
                <span>Total Paid</span>
                <span>₦{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div style="display:flex;gap:1rem;justify-content:center;margin-top:1.5rem">
            <a href="{{ route('orders.index') }}" class="btn btn-outline">View My Orders</a>
            <a href="{{ route('home') }}" class="btn btn-accent">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
