<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart()
    {
        return session()->get('cart', []);
    }

    private function saveCart(array $cart)
    {
        session()->put('cart', $cart);
    }

    public function index()
    {
        $cart  = $this->getCart();
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity'   => 'required|integer|min:1|max:99',
    ]);

    $product = Product::findOrFail($request->product_id);

    if (!$product->isInStock()) {
        return back()->with('error', 'Sorry, this product is out of stock.');
    }

    $cart = $this->getCart();
    $id   = $product->id;

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = min($cart[$id]['quantity'] + $request->quantity, $product->stock);
    } else {
        $cart[$id] = [
            'id'       => $product->id,
            'name'     => $product->name,
            'price'    => $product->price,
            'image'    => $product->image,  // already array due to cast in model
            'slug'     => $product->slug,
            'quantity' => $request->quantity,
            'stock'    => $product->stock,
        ];
    }

    $this->saveCart($cart);

    return back()->with('success', "\"{$product->name}\" added to cart!");
}

    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $cart = $this->getCart();

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = min($request->quantity, $cart[$id]['stock']);
            $this->saveCart($cart);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove($id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }
}
