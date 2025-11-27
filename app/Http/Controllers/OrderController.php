<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.menuItem'])
                      ->latest()
                      ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_amount' => 'required|numeric|min:0',
            'order_type' => 'required|in:Dine In,Take Out',
            'payment_method' => 'required|in:Cash,Card',
        ]);

        // Calculate total
        $total = 0;
        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['id']);
            $total += $menuItem->price * $item['quantity'];
        }

        // Check payment amount
        if ($request->payment_amount < $total) {
            return response()->json([
                'error' => 'Insufficient payment amount'
            ], 422);
        }

        // Create order
        $order = Order::create([
            'order_id' => 'ORD' . Str::random(8),
            'user_id' => auth()->id(),
            'total' => $total,
            'payment_amount' => $request->payment_amount,
            'change_amount' => $request->payment_amount - $total,
            'payment_method' => $request->payment_method,
            'order_type' => $request->order_type,
            'status' => 'pending',
        ]);

        // Create order items and update stock
        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['id']);
            
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $menuItem->price,
            ]);

            // Update stock
            $menuItem->decrement('stock', $item['quantity']);
        }

        // Load relationships for response
        $order->load(['user', 'orderItems.menuItem']);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.menuItem']);
        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,completed'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }
}