@extends('layouts.app')
@section('title', 'Admin — Categories')

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

    /* Modal */
    .modal-backdrop {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }
    .modal-backdrop.open { display: flex; }
    .modal {
        background: #fff;
        border-radius: var(--radius);
        width: 100%; max-width: 500px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: slideUp .2s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to   { transform: translateY(0);   opacity: 1; }
    }
    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    .modal-header h3 { font-size: 1.1rem; font-weight: 700; }
    .modal-close {
        background: none; border: none; font-size: 1.4rem;
        cursor: pointer; color: var(--muted); line-height: 1;
    }
    .modal-close:hover { color: var(--danger); }
    .modal-body { padding: 1.5rem; }
    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex; justify-content: flex-end; gap: .8rem;
    }

    /* Toast */
    .toast {
        position: fixed; bottom: 1.5rem; right: 1.5rem;
        padding: .85rem 1.3rem; border-radius: var(--radius);
        font-weight: 600; font-size: .9rem;
        box-shadow: 0 4px 20px rgba(0,0,0,.15);
        z-index: 9999; display: none;
        animation: fadeIn .2s ease;
    }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    .toast.success { background: #dcfce7; color: #166534; border-left: 4px solid var(--success); }
    .toast.error   { background: #fee2e2; color: #991b1b; border-left: 4px solid var(--danger); }

    .cat-thumb { width: 48px; height: 48px; border-radius: 6px; object-fit: cover; background: var(--surface); display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
    .img-preview { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; display: none; margin-top: .5rem; }
</style>
@endpush

@section('content')
<div class="container">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
        <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem">🗂️ Categories</h1>
        <button class="btn btn-accent" onclick="openCreateModal()">+ New Category</button>
    </div>

    <div class="admin-nav">
        <a href="{{ route('admin.orders.index') }}">Orders</a>
        <a href="{{ route('admin.products.index') }}">Products</a>
        <a href="{{ route('admin.categories.index') }}" class="active">Categories</a>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody">
                    @forelse($categories as $category)
                    <tr id="row-{{ $category->id }}">
                        <td>
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     alt="{{ $category->name }}" class="cat-thumb">
                            @else
                                <div class="cat-thumb">🗂️</div>
                            @endif
                        </td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td style="color:var(--muted);font-size:.85rem">{{ $category->slug }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td style="display:flex;gap:.4rem">
                            <button class="btn btn-sm btn-outline"
                                onclick="openEditModal({{ $category->id }})">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                onclick="openDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}')">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--muted);padding:2rem">
                            No categories yet. Create one!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem">{{ $categories->links() }}</div>
    </div>
</div>

{{-- ── CREATE MODAL ─────────────────────────────────── --}}
<div class="modal-backdrop" id="createModal">
    <div class="modal">
        <div class="modal-header">
            <h3>➕ New Category</h3>
            <button class="modal-close" onclick="closeModal('createModal')">×</button>
        </div>
        <form id="createForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" required
                           placeholder="e.g. Electronics">
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*"
                           onchange="previewImage(this, 'createPreview')">
                    <img id="createPreview" class="img-preview">
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked>
                    <label for="create_is_active" style="margin:0;font-weight:400">Active</label>
                </div>
                <p id="createError" style="color:var(--danger);font-size:.85rem;display:none"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn btn-accent" id="createBtn">Create Category</button>
            </div>
        </form>
    </div>
</div>

{{-- ── EDIT MODAL ───────────────────────────────────── --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3>✏️ Edit Category</h3>
            <button class="modal-close" onclick="closeModal('editModal')">×</button>
        </div>
        <form id="editForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="editCategoryId">
            <div class="modal-body">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Image <span style="color:var(--muted);font-size:.8rem">(leave empty to keep current)</span></label>
                    <img id="editCurrentImage" class="img-preview" style="display:block;margin-bottom:.5rem">
                    <input type="file" name="image" class="form-control" accept="image/*"
                           onchange="previewImage(this, 'editPreview')">
                    <img id="editPreview" class="img-preview">
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:.5rem">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                    <label for="edit_is_active" style="margin:0;font-weight:400">Active</label>
                </div>
                <p id="editError" style="color:var(--danger);font-size:.85rem;display:none"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="editBtn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ── DELETE MODAL ─────────────────────────────────── --}}
<div class="modal-backdrop" id="deleteModal">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <h3>🗑️ Delete Category</h3>
            <button class="modal-close" onclick="closeModal('deleteModal')">×</button>
        </div>
        <div class="modal-body" style="text-align:center;padding:2rem 1.5rem">
            <div style="font-size:3rem;margin-bottom:1rem">⚠️</div>
            <p style="font-size:1rem">Are you sure you want to delete</p>
            <p style="font-weight:700;font-size:1.1rem;margin:.4rem 0 .8rem" id="deleteCategoryName"></p>
            <p style="color:var(--muted);font-size:.88rem">This action cannot be undone.</p>
            <p id="deleteError" style="color:var(--danger);font-size:.85rem;margin-top:.5rem;display:none"></p>
        </div>
        <div class="modal-footer">
            <input type="hidden" id="deleteCategoryId">
            <button type="button" class="btn btn-outline" onclick="closeModal('deleteModal')">Cancel</button>
            <button type="button" class="btn btn-danger" id="deleteBtn" onclick="confirmDelete()">
                Yes, Delete
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="toast" id="toast"></div>

@endsection

@push('scripts')
<script>
    const BASE_URL = "{{ url('admin/categories') }}";
    const CSRF     = "{{ csrf_token() }}";

    // ── Modal helpers ────────────────────────────────────────
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ── Toast ────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className   = `toast ${type}`;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3500);
    }

    // ── Image preview ────────────────────────────────────────
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            preview.src = URL.createObjectURL(input.files[0]);
            preview.style.display = 'block';
        }
    }

    // ── CREATE ───────────────────────────────────────────────
    function openCreateModal() {
        document.getElementById('createForm').reset();
        document.getElementById('createPreview').style.display = 'none';
        document.getElementById('createError').style.display   = 'none';
        openModal('createModal');
    }

    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('createBtn');
        btn.textContent = 'Creating…';
        btn.disabled = true;

        const formData = new FormData(this);

        try {
            const res  = await fetch(`${BASE_URL}`, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                body:    formData,
            });
            const data = await res.json();

            if (data.success) {
                closeModal('createModal');
                showToast(data.message);
                setTimeout(() => location.reload(), 800);
            } else {
                showErrors('createError', data.errors || data.message);
            }
        } catch (err) {
            showErrors('createError', 'Something went wrong. Please try again.');
        } finally {
            btn.textContent = 'Create Category';
            btn.disabled = false;
        }
    });

    // ── EDIT ─────────────────────────────────────────────────
    async function openEditModal(id) {
        document.getElementById('editError').style.display = 'none';
        document.getElementById('editPreview').style.display = 'none';

        try {
            const res      = await fetch(`${BASE_URL}/${id}`);
            const category = await res.json();

            document.getElementById('editCategoryId').value      = category.id;
            document.getElementById('editName').value            = category.name;
            document.getElementById('edit_is_active').checked    = category.is_active == 1;

            const currentImg = document.getElementById('editCurrentImage');
            if (category.image) {
                currentImg.src          = `/storage/${category.image}`;
                currentImg.style.display = 'block';
            } else {
                currentImg.style.display = 'none';
            }

            openModal('editModal');
        } catch (err) {
            showToast('Could not load category data.', 'error');
        }
    }

    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id  = document.getElementById('editCategoryId').value;
        const btn = document.getElementById('editBtn');
        btn.textContent = 'Saving…';
        btn.disabled = true;

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        try {
            const res  = await fetch(`${BASE_URL}/${id}`, {
                method:  'POST', // Laravel needs POST + _method=PUT for file uploads
                headers: { 'X-CSRF-TOKEN': CSRF },
                body:    formData,
            });
            const data = await res.json();

            if (data.success) {
                closeModal('editModal');
                showToast(data.message);
                setTimeout(() => location.reload(), 800);
            } else {
                showErrors('editError', data.errors || data.message);
            }
        } catch (err) {
            showErrors('editError', 'Something went wrong. Please try again.');
        } finally {
            btn.textContent = 'Save Changes';
            btn.disabled = false;
        }
    });

    // ── DELETE ───────────────────────────────────────────────
    function openDeleteModal(id, name) {
        document.getElementById('deleteCategoryId').value   = id;
        document.getElementById('deleteCategoryName').textContent = name;
        document.getElementById('deleteError').style.display = 'none';
        openModal('deleteModal');
    }

    async function confirmDelete() {
        const id  = document.getElementById('deleteCategoryId').value;
        const btn = document.getElementById('deleteBtn');
        btn.textContent = 'Deleting…';
        btn.disabled = true;

        try {
            const res  = await fetch(`${BASE_URL}/${id}`, {
                method:  'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();

            if (data.success) {
                closeModal('deleteModal');
                showToast(data.message);
                // Remove row from table without reload
                const row = document.getElementById(`row-${id}`);
                if (row) row.remove();
            } else {
                document.getElementById('deleteError').textContent    = data.message;
                document.getElementById('deleteError').style.display  = 'block';
            }
        } catch (err) {
            document.getElementById('deleteError').textContent   = 'Something went wrong.';
            document.getElementById('deleteError').style.display = 'block';
        } finally {
            btn.textContent = 'Yes, Delete';
            btn.disabled = false;
        }
    }

    // ── Error display helper ─────────────────────────────────
    function showErrors(elementId, errors) {
        const el = document.getElementById(elementId);
        if (typeof errors === 'object') {
            el.textContent = Object.values(errors).flat().join(' ');
        } else {
            el.textContent = errors;
        }
        el.style.display = 'block';
    }
</script>
@endpush
