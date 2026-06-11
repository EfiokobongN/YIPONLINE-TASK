@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="container" style="max-width:860px">
    <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:1.5rem">My Orders</h1>

    @if($orders->isEmpty())
        <div class="card card-body" style="text-align:center;padding:3rem">
            <p style="font-size:1.1rem;color:var(--muted);margin-bottom:1.2rem">You haven't placed any orders yet.</p>
            <a href="{{ route('home') }}" class="btn btn-accent">Start Shopping</a>
        </div>
    @else
        @foreach($orders as $order)
        <div class="card" style="margin-bottom:1.2rem">
            <div style="padding:1.2rem;display:flex;flex-wrap:wrap;align-items:center;gap:1rem;border-bottom:1px solid var(--border)">
                <div>
                    <p style="font-size:.8rem;color:var(--muted)">Order Number</p>
                    <p style="font-weight:700">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p style="font-size:.8rem;color:var(--muted)">Date</p>
                    <p>{{ $order->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p style="font-size:.8rem;color:var(--muted)">Total</p>
                    <p style="font-weight:700">₦{{ number_format($order->total, 2) }}</p>
                </div>
                <div style="margin-left:auto">
                    <span class="badge {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
            <div style="padding:1rem">
                @foreach($order->items as $item)
                <div style="display:flex;justify-content:space-between;font-size:.9rem;padding:.3rem 0">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span>₦{{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div>{{ $orders->links() }}</div>
    @endif
</div>
@endsection
