<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\AuditLogger;

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
        $before = $order->status;
        $order->update(['status' => 'preparing']);

        AuditLogger::log('kitchen.order.status_changed', [
            'order_id' => $order->order_id,
            'from' => $before,
            'to' => 'preparing',
        ], request(), Order::class, $order->id);

        return redirect()->route('kitchen.display')->with('success', 'Order started preparing');
    }

    public function completeOrder(Order $order)
    {
        $before = $order->status;
        $order->update(['status' => 'completed']);

        AuditLogger::log('kitchen.order.status_changed', [
            'order_id' => $order->order_id,
            'from' => $before,
            'to' => 'completed',
        ], request(), Order::class, $order->id);

        return redirect()->route('kitchen.display')->with('success', 'Order completed');
    }
}