@extends('layouts.app')

@section('content')
<div class="w-full h-full" style="background-color: #1f2937; min-height: 100vh;">
    <nav class="text-white p-4 shadow-lg" style="background-color: #dc2626;">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Kuya Benz</h1>
                <p class="text-sm opacity-90">Kitchen Display</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-6 py-2 bg-white rounded-lg font-semibold" style="color: #dc2626;">
                    Logout
                </button>
            </form>
        </div>
    </nav>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($orders->count() === 0)
                <div class="col-span-full text-center text-gray-400 py-12 text-xl">
                    <div class="text-6xl mb-4">👨‍🍳</div>
                    <p>No pending orders</p>
                    <p class="text-sm mt-2">Waiting for new orders...</p>
                </div>
            @else
                @foreach($orders as $order)
                    <div class="bg-white rounded-xl shadow-lg p-6 transform transition hover:scale-105">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold" style="color: #1f2937;">Order #{{ $order->order_id }}</h3>
                                <p class="text-sm text-gray-600">{{ $order->created_at->format('h:i A') }}</p>
                                <p class="text-sm font-semibold mt-1" style="color: #f59e0b;">{{ $order->order_type }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold text-white 
                                {{ $order->status === 'preparing' ? 'bg-orange-500' : 'bg-gray-500' }}">
                                {{ $order->status === 'preparing' ? 'Preparing' : 'New' }}
                            </span>
                        </div>
                        
                        <div class="mb-3 p-2 bg-blue-50 rounded border-l-4" style="border-color: #dc2626;">
                            <p class="text-xs text-gray-600">Cashier: <span class="font-semibold">{{ $order->user->username }}</span></p>
                        </div>
                        
                        <div class="space-y-2 mb-4">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Order Items:</p>
    @foreach($order->orderItems as $item)
        <div class="flex items-center gap-3 p-2 bg-gray-50 rounded">
            <!-- Item Image -->
            <div class="w-10 h-10 bg-gray-300 rounded overflow-hidden flex-shrink-0">
                <img src="{{ $item->menuItem->image_url }}" 
                     alt="{{ $item->menuItem->name }}" 
                     class="w-full h-full object-cover"
                     onerror="this.src='/images/default-food.png'">
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-800 text-sm">{{ $item->menuItem->name }}</p>
                <p class="text-xs text-gray-600">{{ $item->menuItem->category }}</p>
            </div>
            <span class="text-lg font-bold text-red-600">×{{ $item->quantity }}</span>
        </div>
    @endforeach
</div>
                        
                        @if($order->status === 'pending')
                            <form method="POST" action="{{ route('kitchen.start-preparing', $order) }}">
                                @csrf
                                <button type="submit" class="w-full text-white py-3 rounded-lg font-semibold mb-2 hover:bg-orange-600 transition" style="background-color: #f59e0b;">
                                    Start Preparing
                                </button>
                            </form>
                        @endif
                        
                        @if($order->status === 'preparing')
                            <form method="POST" action="{{ route('kitchen.complete-order', $order) }}">
                                @csrf
                                <button type="submit" class="w-full text-white py-3 rounded-lg font-semibold hover:bg-green-600 transition" style="background-color: #10b981;">
                                    Mark as Complete
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<script>
    // Auto-refresh every 10 seconds
    setInterval(() => {
        window.location.reload();
    }, 10000);
</script>
@endsection