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

    // Add nickname dynamically from session
    $orders->each(function($order) {
        $order->nickname = session("order_nickname_{$order->id}") ?? $order->user->username;
    });

    return view('kitchen.display', compact('orders'));
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