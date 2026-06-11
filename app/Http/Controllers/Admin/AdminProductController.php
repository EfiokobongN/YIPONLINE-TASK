<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    // ─── PRODUCTS ────────────────────────────────────────────

    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.form', [
            'product'    => new Product(),
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $data['slug'] = Str::slug($data['name']);

        // Handle multiple images as JSON
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('products', 'public');
            }
            $data['image'] = json_encode($paths);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories  = Category::where('is_active', true)->get();
        $existingImages = $product->image ? json_decode($product->image, true) : [];
        return view('admin.products.form', compact('product', 'categories', 'existingImages'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);

        // Handle new images uploaded
        if ($request->hasFile('images')) {
            // Delete old images from storage
            if ($product->image) {
                foreach (json_decode($product->image, true) as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            // Store new images
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('products', 'public');
            }
            $data['image'] = json_encode($paths);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        // Delete images from storage
        if ($product->image) {
            foreach (json_decode($product->image, true) as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    // ─── CATEGORIES ──────────────────────────────────────────

public function categoryIndex()
{
    $categories = Category::withCount('products')->latest()->paginate(20);
    return view('admin.categories.index', compact('categories'));
}

public function categoryStore(Request $request)
{
    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'is_active'   => 'nullable|boolean',
    ]);

    $data['slug']      = Str::slug($data['name']);
    $data['is_active'] = $request->boolean('is_active', true);

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('categories', 'public');
    }

    $category = Category::create($data);

    return response()->json([
        'success'  => true,
        'message'  => 'Category created successfully.',
        'category' => $category,
    ]);
}

public function categoryUpdate(Request $request, Category $category)
{
    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'is_active'   => 'nullable|boolean',
    ]);

    $data['is_active'] = $request->boolean('is_active', true);

    if ($request->hasFile('image')) {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $data['image'] = $request->file('image')->store('categories', 'public');
    }

    $category->update($data);

    return response()->json([
        'success'  => true,
        'message'  => 'Category updated successfully.',
        'category' => $category->fresh(),
    ]);
}

public function categoryDestroy(Category $category)
{
    if ($category->products()->count() > 0) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot delete category that has products assigned to it.',
        ], 422);
    }

    if ($category->image) {
        Storage::disk('public')->delete($category->image);
    }

    $category->delete();

    return response()->json([
        'success' => true,
        'message' => 'Category deleted successfully.',
    ]);
}

// Fetch single category for edit modal
public function categoryShow(Category $category)
{
    return response()->json($category);
}
    // ─── VALIDATION ──────────────────────────────────────────

    private function validateProduct(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'category_id'   => 'required|exists:categories,id',
            'images.*'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
        ]);
    }

    private function validateCategory(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
        ]);
    }
}
