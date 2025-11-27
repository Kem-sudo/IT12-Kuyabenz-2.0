<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class CashierController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::all();
        return view('cashier.pos', compact('menuItems'));
    }

    public function processOrder(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required',
                'payment_amount' => 'required|numeric|min:0',
                'order_type' => 'required|in:Dine In,Take Out',
            ]);

            // Parse the items JSON
            $items = json_decode($request->items, true);
            
            if (empty($items)) {
                return back()->withErrors(['error' => 'No items in order']);
            }

            $total = 0;

            // Calculate total and validate stock
            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['id']);
                if (!$menuItem) {
                    return back()->withErrors(['error' => 'Menu item not found: ' . $item['name']]);
                }
                if ($menuItem->stock < $item['quantity']) {
                    return back()->withErrors(['error' => "Insufficient stock for {$menuItem->name}. Available: {$menuItem->stock}"]);
                }
                $subtotal = $menuItem->price * $item['quantity'];
                $total += $subtotal;
            }

            if ($request->payment_amount < $total) {
                return back()->withErrors(['payment' => 'Insufficient payment amount. Total: ₱' . number_format($total, 2)]);
            }

            // Create order
            $order = Order::create([
                'order_id' => 'ORD' . Str::random(8),
                'user_id' => auth()->id(),
                'total' => $total,
                'payment_amount' => $request->payment_amount,
                'change_amount' => $request->payment_amount - $total,
                'payment_method' => 'Cash',
                'order_type' => $request->order_type,
                'status' => 'pending',
            ]);

            // Create order items and update stock
            foreach ($items as $item) {
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

            return redirect()->route('cashier.receipt', $order->id);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function showReceipt(Order $order)
    {
        $order->load(['user', 'orderItems.menuItem']);
        return view('cashier.receipt', compact('order'));
    }
}