<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CashierController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::all();
        return view('Cashier.Pos', compact('menuItems'));
    }

    /*
    |----------------------------------------------------------------------
    | PROCESS ORDER
    |----------------------------------------------------------------------
    */
    public function processOrder(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required',
                'payment_amount' => 'required|numeric|min:0',
                'order_type' => 'required|in:Dine In,Take Out',
                'nickname' => 'nullable|string|max:50',
            ]);

            $items = json_decode($request->items, true);

            if (empty($items)) {
                return back()->withErrors(['error' => 'No items in order']);
            }

            $total = 0;

            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['id']);

                if (!$menuItem) {
                    return back()->withErrors([
                        'error' => 'Menu item not found: ' . $item['name']
                    ]);
                }

                if ($menuItem->stock < $item['quantity']) {
                    return back()->withErrors([
                        'error' => "Insufficient stock for {$menuItem->name}"
                    ]);
                }

                $total += $menuItem->price * $item['quantity'];
            }

            if ($request->payment_amount < $total) {
                return back()->withErrors([
                    'payment' => 'Insufficient payment amount.'
                ]);
            }

            $order = Order::create([
                'order_id' => 'ORD' . Str::random(8),
                'user_id' => auth()->id(),
                'nickname' => $request->nickname,
                'total' => $total,
                'payment_amount' => $request->payment_amount,
                'change_amount' => $request->payment_amount - $total,
                'payment_method' => 'Cash',
                'order_type' => $request->order_type,
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                ]);

                $menuItem->decrement('stock', $item['quantity']);
            }

            return redirect()->route('cashier.receipt', [
                'order' => $order->id,
                'nickname' => $request->nickname ?? ''
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | RECEIPT
    |----------------------------------------------------------------------
    */
    public function showReceipt(Order $order, Request $request)
    {
        $order->load(['user', 'orderItems.menuItem']);

        return view('Cashier.receipt', [
            'order' => $order,
            'nickname' => $request->query('nickname')
        ]);
    }

    /*
    |----------------------------------------------------------------------
    | ADMIN VALIDATION (USED BY AJAX)
    |----------------------------------------------------------------------
    */
    public function validateAdmin(Request $request)
    {
        $admin = User::where('username', $request->username)
            ->where('role', 'admin')
            ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true]);
    }

    /*
    |----------------------------------------------------------------------
    | OPTIONAL: ACTUAL ITEM REMOVAL (SAFE SERVER VERSION)
    |----------------------------------------------------------------------
    | (Use this ONLY if you later want server-side deletion logs)
    */
    public function removeItemFromOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'menu_item_id' => 'required',
        ]);

        $orderItem = OrderItem::where('order_id', $request->order_id)
            ->where('menu_item_id', $request->menu_item_id)
            ->first();

        if (!$orderItem) {
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }

        // restore stock
        $menuItem = MenuItem::find($request->menu_item_id);
        if ($menuItem) {
            $menuItem->increment('stock', $orderItem->quantity);
        }

        $orderItem->delete();

        return response()->json(['success' => true]);
    }
}