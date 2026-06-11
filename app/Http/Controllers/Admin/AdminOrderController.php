<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $orders   = $query->paginate(20)->withQueryString();
        $statuses = Order::statuses();

        // Summary stats
        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'revenue'   => Order::where('status', 'delivered')->sum('total'),
            'today'     => Order::whereDate('created_at', today())->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statuses', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user.customer', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Order::statuses()),
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Order {$order->order_number} status updated to {$request->status}.");
    }
}
