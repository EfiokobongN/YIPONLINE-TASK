<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user     = auth()->user();
        $customer = Customer::firstOrNew(['user_id' => $user->id]);

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax      = round($subtotal * 0.075, 2);
        $shipping = $subtotal >= 50000 ? 0 : 1500;
        $total    = $subtotal + $tax + $shipping;

        return view('checkout.index', compact(
            'cart', 'subtotal', 'tax', 'shipping', 'total', 'user', 'customer'
        ));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:500',
            'city'    => 'required|string|max:100',
            'state'   => 'required|string|max:100',
            'zip'     => 'nullable|string|max:20',
            'notes'   => 'nullable|string',
            'name'    => 'nullable|string|max:255',
            'email'   => 'nullable|email',
            'phone'   => 'nullable|string|max:20',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Cart is empty.');
        }

        $user     = auth()->user();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax      = round($subtotal * 0.075, 2);
        $shipping = $subtotal >= 50000 ? 0 : 1500;
        $total    = $subtotal + $tax + $shipping;

        DB::transaction(function () use ($request, $cart, $subtotal, $tax, $shipping, $total, $user, &$order) {

            // Update or create customer record
            Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'             => $request->filled('name')  ? $request->name  : $user->name,
                    'email'            => $request->filled('email') ? $request->email : $user->email,
                    'phone'            => $request->filled('phone') ? $request->phone : $user->phone,
                    'shipping_address' => $request->address,
                    'shipping_city'    => $request->city,
                    'shipping_state'   => $request->state,
                    'shipping_zip'     => $request->zip,
                ]
            );

            // Also update users table if name/email/phone changed
            $userUpdates = [];
            if ($request->filled('name')  && $request->name  !== $user->name)  $userUpdates['name']  = $request->name;
            if ($request->filled('email') && $request->email !== $user->email) $userUpdates['email'] = $request->email;
            if ($request->filled('phone') && $request->phone !== $user->phone) $userUpdates['phone'] = $request->phone;
            if (!empty($userUpdates)) $user->update($userUpdates);

            // Create order
            $order = Order::create([
                'user_id'  => $user->id,
                'status'   => Order::STATUS_PENDING,
                'subtotal' => $subtotal,
                'tax'      => $tax,
                'shipping' => $shipping,
                'total'    => $total,
                'notes'    => $request->notes,
                'paid_at'  => now(),
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['id'],
                    'product_name'  => $item['name'],
                    'product_image' => $item['image'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['price'],
                    'subtotal'      => $item['price'] * $item['quantity'],
                ]);
            }
        });

        session()->forget('cart');

        return redirect()->route('checkout.success', $order)
                         ->with('success', 'Order placed successfully!');
    }

    public function updateShippingInfo(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'zip'     => 'nullable|string|max:20',
        ]);

        $user = auth()->user();

        // Update customers table
        Customer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'             => $request->name,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'shipping_address' => $request->address,
                'shipping_city'    => $request->city,
                'shipping_state'   => $request->state,
                'shipping_zip'     => $request->zip,
            ]
        );

        // Also sync name/email/phone back to users table
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Shipping information updated successfully.');
    }

    public function success(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        return view('checkout.success', compact('order'));
    }

    public function myOrders()
    {
        $orders = auth()->user()->orders()->with('items')->latest()->paginate(10);
        return view('checkout.orders', compact('orders'));
    }
}
