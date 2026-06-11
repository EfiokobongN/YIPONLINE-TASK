@extends('layouts.app')
@section('title', 'Admin — Products')

@push('styles')
<style>
    .admin-nav { display: flex; gap: .8rem; margin-bottom: 1.5rem; }
    .admin-nav a { padding: .5rem 1.1rem; border-radius: 6px; font-weight: 600; font-size: .9rem; background: #fff; border: 1.5px solid var(--border); color: var(--text); }
    .admin-nav a.active { background: var(--brand); color: #fff; border-color: var(--brand); }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: var(--surface); padding: .9rem 1rem; text-align: left; font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); border-bottom: 2px solid var(--border); }
    .data-table td { padding: .9rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; font-size: .93rem; }
    .data-table tr:hover td { background: #fafafa; }
    .table-wrap { overflow-x: auto; }
    .product-thumb { width: 50px; height: 50px; border-radius: 6px; object-fit: cover; background: var(--surface); display: flex; align-items: center; justify-content: center; }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
        <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem">🛠️ Products</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-accent">+ Add Product</a>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.orders.index') }}">Orders</a>
        <a href="{{ route('admin.categories.index') }}">Categories</a>
        <a href="{{ route('admin.products.index') }}" class="active">Products</a>
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
                    <tr>
                        <td>
                            @if($p->image)
                                <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" class="product-thumb">
                            @else
                                <div class="product-thumb" style="background:var(--surface)">🛍️</div>
                            @endif
                        </td>
                        <td><strong>{{ $p->name }}</strong></td>
                        <td>{{ $p->category }}</td>
                        <td>₦{{ number_format($p->price, 2) }}</td>
                        <td>
                            <span class="{{ $p->stock < 5 ? 'badge badge-danger' : '' }}">{{ $p->stock }}</span>
                        </td>
                        <td>
                            @if($p->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td style="display:flex;gap:.4rem">
                            <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $p) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete {{ addslashes($p->name) }}?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:2rem">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem">{{ $products->links() }}</div>
    </div>
</div>
@endsection
