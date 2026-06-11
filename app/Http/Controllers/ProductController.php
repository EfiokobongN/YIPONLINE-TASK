<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $query = Product::active();

    if ($request->category_id) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->sort === 'price_asc') {
        $query->orderBy('price', 'asc');
    } elseif ($request->sort === 'price_desc') {
        $query->orderBy('price', 'desc');
    } elseif ($request->sort === 'newest') {
        $query->latest();
    }

    $products   = $query->paginate(12)->withQueryString();
    $categories = Category::where('is_active', true)->get(); // ← fetch from categories table

    return view('products.index', compact('products', 'categories'));
}

public function show($slug)
{
    $product = Product::active()->with('category')->where('slug', $slug)->firstOrFail();

    // Related products via category_id not category string
    $related = Product::active()
        ->with('category')
        ->where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->take(4)
        ->get();

    return view('products.show', compact('product', 'related'));
}

public function search(Request $request)
{
    $q = $request->q;

    $products = Product::active()
        ->with('category')
        ->where(function($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
        })
        ->paginate(12)
        ->withQueryString();

    $categories = Category::where('is_active', true)->get();

    return view('products.index', compact('products', 'categories'))
           ->with('searchQuery', $q);
}
}
