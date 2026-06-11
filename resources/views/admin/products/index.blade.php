@extends('layouts.app')
@section('title', 'Admin — Products')

@push('styles')
<style>
    .admin-nav { display: flex; gap: .5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); }
    .admin-nav a {
        padding: .65rem 1.2rem; border-radius: 8px 8px 0 0;
        font-weight: 600; font-size: .9rem;
        background: #fff; border: 1.5px solid var(--border);
        border-bottom: none; color: var(--muted);
        margin-bottom: -2px; text-decoration: none;
    }
    .admin-nav a.active { color: var(--brand); border-bottom-color: #fff; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: var(--surface); padding: .9rem 1rem; text-align: left; font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); border-bottom: 2px solid var(--border); }
    .data-table td { padding: .9rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; font-size: .93rem; }
    .data-table tr:hover td { background: #fafafa; }
    .table-wrap { overflow-x: auto; }
    .product-thumb {
        width: 52px; height: 52px; border-radius: 8px;
        object-fit: cover; display: block;
    }
    .thumb-placeholder {
        width: 52px; height: 52px; border-radius: 8px;
        background: var(--surface); display: flex;
        align-items: center; justify-content: center;
        font-size: 1.4rem; border: 1.5px solid var(--border);
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
        <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem">🛍️ Products</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-accent">+ Add Product</a>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.orders.index') }}">📦 Orders</a>
        <a href="{{ route('admin.categories.index') }}">🗂️ Categories</a>
        <a href="{{ route('admin.products.index') }}" class="active">🛍️ Products</a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    @php
                        // Handle JSON array of images
                        $images   = is_array($p->image)
                                    ? $p->image
                                    : json_decode($p->image, true);
                        $firstImg = is_array($images) ? ($images[0] ?? null) : null;
                    @endphp
                    <tr>
                        {{-- Image: show first from JSON array --}}
                        <td>
                            @if($firstImg)
                                <img src="{{ asset('storage/' . $firstImg) }}"
                                     alt="{{ $p->name }}" class="product-thumb">
                            @else
                                <div class="thumb-placeholder">🛍️</div>
                            @endif
                        </td>

                        <td><strong>{{ $p->name }}</strong></td>

                        {{-- Category: use relationship name not raw object --}}
                        <td>{{ $p->category?->name ?? '—' }}</td>

                        <td>₦{{ number_format($p->price, 2) }}</td>

                        <td>
                            @if($p->stock < 5)
                                <span class="badge badge-danger">{{ $p->stock }}</span>
                            @else
                                {{ $p->stock }}
                            @endif
                        </td>

                        <td>
                            @if($p->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>

                        <td style="display:flex;gap:.4rem">
                            <a href="{{ route('admin.products.edit', $p) }}"
                               class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $p) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete {{ addslashes($p->name) }}?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--muted);padding:2rem">
                            No products yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem">{{ $products->links() }}</div>
    </div>
</div>
@endsection
