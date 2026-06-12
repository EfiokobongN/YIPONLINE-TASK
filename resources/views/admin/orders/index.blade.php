@extends('layouts.app')
@section('title', 'Admin — Orders')

@push('styles')
<style>
    .admin-header { background: var(--brand); color: #fff; padding: 1.5rem; border-radius: var(--radius); margin-bottom: 1.5rem; }
    .admin-header h1 { font-family: 'Playfair Display', serif; font-size: 1.6rem; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-card { background: #fff; border-radius: var(--radius); padding: 1.2rem; box-shadow: var(--shadow); text-align: center; }
    .stat-number { font-size: 1.8rem; font-weight: 700; color: var(--brand); }
    .stat-label { font-size: .82rem; color: var(--muted); margin-top: .2rem; }
    .filter-bar { display: flex; gap: .8rem; flex-wrap: wrap; margin-bottom: 1.2rem; align-items: center; }
    .filter-btn { padding: .4rem .9rem; border-radius: 20px; border: 1.5px solid var(--border); background: #fff; cursor: pointer; font-size: .85rem; text-decoration: none; color: var(--text); }
    .filter-btn.active { background: var(--brand); color: #fff; border-color: var(--brand); }
    .table-wrap { overflow-x: auto; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: var(--surface); padding: .9rem 1rem; text-align: left; font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); border-bottom: 2px solid var(--border); }
    .data-table td { padding: .9rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; font-size: .93rem; }
    .data-table tr:hover td { background: #fafafa; }
    .status-select { padding: .35rem .6rem; border-radius: 6px; border: 1.5px solid var(--border); font-size: .85rem; cursor: pointer; }

    /* Admin Nav Tabs */
    .admin-nav { display: flex; gap: .5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); padding-bottom: 0; }
    .admin-nav a {
        padding: .65rem 1.2rem; border-radius: 8px 8px 0 0;
        font-weight: 600; font-size: .9rem;
        background: #fff; border: 1.5px solid var(--border);
        border-bottom: none; color: var(--muted);
        margin-bottom: -2px; text-decoration: none;
        transition: color .15s, background .15s;
    }
    .admin-nav a:hover { color: var(--brand); background: var(--surface); }
    .admin-nav a.active { background: #fff; color: var(--brand); border-color: var(--border); border-bottom-color: #fff; }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .admin-nav { flex-wrap: wrap; }
    }
</style>
@endpush

@section('content')
<div class="container">

    <div class="admin-header">
        <h1>🛠️ Admin Dashboard</h1>
        <p style="opacity:.8;margin-top:.3rem">Manage orders and monitor sales</p>
    </div>

    {{-- Nav Tabs --}}
    <div class="admin-nav">
        <a href="{{ route('admin.orders.index') }}" class="active">📦 Orders</a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color:#92400e">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color:var(--success)">₦{{ number_format($stats['revenue'], 0) }}</div>
            <div class="stat-label">Revenue (Delivered)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color:#1e40af">{{ $stats['today'] }}</div>
            <div class="stat-label">Today's Orders</div>
        </div>
    </div>

    {{-- Status Filters --}}
    <div class="filter-bar">
        <a href="{{ route('admin.orders.index') }}"
           class="filter-btn {{ !request('status') ? 'active' : '' }}">All</a>
        @foreach($statuses as $s)
            <a href="{{ route('admin.orders.index', ['status' => $s]) }}"
               class="filter-btn {{ request('status') === $s ? 'active' : '' }}">
                {{ ucfirst($s) }}
            </a>
        @endforeach

        <form method="GET" action="{{ route('admin.orders.index') }}" style="margin-left:auto">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" placeholder="Search order/customer…"
                   value="{{ request('search') }}"
                   class="form-control" style="width:220px;padding:.4rem .8rem;font-size:.88rem">
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            <div>{{ $order->shipping_name }}</div>
                            <div style="font-size:.8rem;color:var(--muted)">{{ $order->shipping_email }}</div>
                        </td>
                        <td>{{ $order->items->count() }} item(s)</td>
                        <td><strong>₦{{ number_format($order->total, 2) }}</strong></td>
                        <td>
                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                                @csrf @method('PATCH')
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    @foreach($statuses as $s)
                                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td style="font-size:.85rem;color:var(--muted)">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="btn btn-sm btn-outline">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--muted);padding:2rem">
                            No orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem">{{ $orders->links() }}</div>
    </div>

</div>
@endsection
