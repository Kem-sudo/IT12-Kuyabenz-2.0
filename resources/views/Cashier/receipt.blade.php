<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Kuya Benz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt-content, #receipt-content * {
                visibility: visible;
            }
            #receipt-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="w-full h-full flex items-center justify-center p-6" style="background: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%); min-height: 100vh;">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="p-8" id="receipt-content">
                <div class="text-center mb-6 border-b-2 border-dashed border-gray-300 pb-6">
                    <h1 class="text-3xl font-bold mb-2" style="color: #dc2626;">Kuya Benz</h1>
                    <p class="text-gray-600 mb-3">Delicious Filipino Cuisine</p>
                    <p class="text-sm text-gray-600">Official Receipt</p>
                </div>
                
                <div class="mb-6 text-sm space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order #:</span>
                        <span class="font-semibold">{{ $order->order_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-semibold">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Time:</span>
                        <span class="font-semibold">{{ $order->created_at->format('h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cashier:</span>
                        <span class="font-semibold">{{ $order->user->username }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Type:</span>
                        <span class="font-semibold">{{ $order->order_type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment:</span>
                        <span class="font-semibold">💵 Cash</span>
                    </div>
                </div>
                
                <div class="border-t-2 border-dashed border-gray-300 pt-4 mb-4">
                    <table class="w-full text-sm mb-4">
                        <thead>
                            <tr class="border-b border-gray-300">
                                <th class="text-left py-2">Item</th>
                                <th class="text-center py-2">Qty</th>
                                <th class="text-right py-2">Price</th>
                                <th class="text-right py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr class="border-b border-gray-200">
                                    <td class="py-2">{{ $item->menuItem->name }}</td>
                                    <td class="text-center py-2">{{ $item->quantity }}</td>
                                    <td class="text-right py-2">₱{{ number_format($item->price, 2) }}</td>
                                    <td class="text-right py-2 font-semibold">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="space-y-2 border-t-2 border-gray-300 pt-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span>TOTAL:</span>
                            <span style="color: #dc2626;">₱{{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Payment:</span>
                            <span class="font-semibold">₱{{ number_format($order->payment_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold" style="color: #10b981;">
                            <span>CHANGE:</span>
                            <span>₱{{ number_format($order->change_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center text-xs text-gray-500 border-t border-dashed border-gray-300 pt-4">
                    <p class="mb-1">Thank you for your order!</p>
                    <p>Please come again</p>
                </div>
            </div>
            
            <div class="p-4 bg-gray-50 rounded-b-xl flex gap-2 no-print">
                <button onclick="window.print()" class="flex-1 py-3 text-white rounded-lg font-semibold" style="background-color: #10b981;">
                    🖨️ Print Receipt
                </button>
                <a href="{{ route('cashier.pos') }}" class="flex-1 py-3 text-white rounded-lg font-semibold text-center block" style="background-color: #dc2626;">
                    New Order
                </a>
            </div>
        </div>
    </div>
</body>
</html>