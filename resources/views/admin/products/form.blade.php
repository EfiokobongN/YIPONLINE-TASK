@extends('layouts.app')
@section('title', $product->exists ? 'Edit Product' : 'New Product')

@push('styles')
<style>
    .image-preview-grid {
        display: flex; flex-wrap: wrap; gap: .6rem; margin-top: .6rem;
    }
    .image-preview-item {
        position: relative; width: 90px; height: 90px;
    }
    .image-preview-item img {
        width: 100%; height: 100%; object-fit: cover;
        border-radius: 8px; border: 2px solid var(--border);
    }
    .image-preview-item .remove-img {
        position: absolute; top: -6px; right: -6px;
        background: var(--danger); color: #fff;
        border: none; border-radius: 50%;
        width: 20px; height: 20px; font-size: .75rem;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        line-height: 1;
    }
    .upload-area {
        border: 2px dashed var(--border); border-radius: var(--radius);
        padding: 1.5rem; text-align: center; cursor: pointer;
        transition: border-color .2s, background .2s;
    }
    .upload-area:hover { border-color: var(--brand); background: #f0f4ff; }
    .upload-area p { color: var(--muted); font-size: .9rem; margin-top: .4rem; }
</style>
@endpush

@section('content')
<div class="container" style="max-width:870px">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm">← Products</a>
        <h1 style="font-family:'Playfair Display',serif;font-size:1.5rem">
            {{ $product->exists ? 'Edit: ' . $product->name : 'Add New Product' }}
        </h1>
    </div>

    <div class="card card-body">
        <form method="POST"
              action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
              enctype="multipart/form-data"
              id="productForm">
            @csrf
            @if($product->exists) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-error" style="max-width:none;margin-bottom:1rem">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
            @endif

            {{-- Name --}}
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" name="name" id="name" class="form-control"
                       value="{{ old('name', $product->name) }}" required>
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea name="description" id="description" class="form-control"
                          rows="4" required>{{ old('description', $product->description) }}</textarea>
            </div>

            {{-- Price & Compare Price --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="price">Price (₦) *</label>
                    <input type="number" name="price" id="price" class="form-control"
                           step="0.01" value="{{ old('price', $product->price) }}" required>
                </div>
                <div class="form-group">
                    <label for="compare_price">Compare Price (₦)</label>
                    <input type="number" name="compare_price" id="compare_price"
                           class="form-control" step="0.01"
                           value="{{ old('compare_price', $product->compare_price) }}">
                </div>
            </div>

            {{-- Stock & Category --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="stock">Stock Quantity *</label>
                    <input type="number" name="stock" id="stock" class="form-control"
                           value="{{ old('stock', $product->stock) }}" required min="0">
                </div>
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">— Select Category —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Product Images (multiple) --}}
            <div class="form-group">
                <label>Product Images</label>

                {{-- Existing images on edit --}}
                @if($product->exists && $product->image)
                    <div class="image-preview-grid" id="existingImages">
                        @foreach((is_array($product->image) ? $product->image : json_decode($product->image, true)) as $index => $img)
                            <div class="image-preview-item" id="existing-{{ $index }}">
                                <img src="{{ asset('storage/' . $img) }}" alt="Image {{ $index + 1 }}">
                                <button type="button" class="remove-img"
                                        onclick="removeExistingImage({{ $index }}, '{{ $img }}')">×</button>
                                <input type="hidden" name="existing_images[]" value="{{ $img }}">
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload area --}}
                <div class="upload-area" onclick="document.getElementById('images').click()">
                    <div style="font-size:2rem">📁</div>
                    <p><strong>Click to upload</strong> or drag & drop</p>
                    <p>JPG, PNG, WEBP — max 2MB each — multiple allowed</p>
                </div>
                <input type="file" name="images[]" id="images" multiple accept="image/*"
                       style="display:none" onchange="previewNewImages(this)">

                {{-- New image previews --}}
                <div class="image-preview-grid" id="newImagePreviews"></div>
            </div>

            {{-- Active & Featured --}}
            <div style="display:flex;gap:2rem;margin-bottom:1.2rem">
                <label style="display:flex;align-items:center;gap:.5rem;font-weight:400">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                    Active (visible on store)
                </label>
                <label style="display:flex;align-items:center;gap:.5rem;font-weight:400">
                    <input type="checkbox" name="is_featured" value="1"
                           {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    Featured
                </label>
            </div>

            <div style="display:flex;gap:.8rem">
                <button type="submit" class="btn btn-accent">
                    {{ $product->exists ? 'Save Changes' : 'Create Product' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview newly selected images
    function previewNewImages(input) {
        const container = document.getElementById('newImagePreviews');
        container.innerHTML = '';

        Array.from(input.files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${i + 1}">
                    <button type="button" class="remove-img" onclick="this.parentElement.remove()">×</button>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // Remove an existing image on edit
    function removeExistingImage(index, path) {
        document.getElementById(`existing-${index}`).remove();
    }
</script>
@endpush
