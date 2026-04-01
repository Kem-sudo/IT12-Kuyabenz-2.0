<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class KitchenController extends Controller
{
    public function index()
{
    $orders = Order::with(['user', 'orderItems.menuItem'])
                   ->whereIn('status', ['pending', 'preparing'])
                   ->latest()
                   ->get();

    // Prefer stored nickname; fall back to legacy session data for older orders
    $orders->each(function ($order) {
        if ($order->nickname === null || $order->nickname === '') {
            $order->nickname = session("order_nickname_{$order->id}");
        }
    });

    return view('Kitchen.display', compact('orders'));
}


    public function startPreparing(Order $order)
    {
        $order->update(['status' => 'preparing']);
        return redirect()->route('kitchen.display')->with('success', 'Order started preparing');
    }

    public function completeOrder(Order $order)
    {
        $order->update(['status' => 'completed']);
        return redirect()->route('kitchen.display')->with('success', 'Order completed');
    }
}