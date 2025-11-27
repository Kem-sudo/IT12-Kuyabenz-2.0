@extends('layouts.app')

@section('content')
<div class="w-full h-full" style="background-color: #111827; min-height: 100vh;">
    <nav class="text-white p-4 shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <span class="live-indicator" style="width: 12px; height: 12px;"></span>
                <div>
                    <h1 class="text-2xl font-bold">Kuya Benz</h1>
                    <p class="text-sm opacity-90">Real-Time Transaction Monitor</p>
                </div>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-white rounded-lg font-semibold" style="color: #10b981;">
                Back to Dashboard
            </a>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto p-6">
        <!-- Real-time Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium opacity-90">Active Orders</h3>
                    @if($activeOrders > 0)
                        <span class="live-indicator" style="background-color: white;"></span>
                    @endif
                </div>
                <p class="text-4xl font-bold">{{ $activeOrders }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-sm font-medium opacity-90 mb-2">Today's Orders</h3>
                <p class="text-4xl font-bold">{{ $completedToday }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-sm font-medium opacity-90 mb-2">Today's Sales</h3>
                <p class="text-4xl font-bold">₱{{ number_format($todaySales, 0) }}</p>
            </div>
            
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="text-sm font-medium opacity-90 mb-2">Last 24 Hours</h3>
                <p class="text-4xl font-bold">{{ $recentOrders->count() }}</p>
            </div>
        </div>
        
        <!-- Transaction Stream -->
        <div class="bg-gray-800 rounded-xl shadow-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="live-indicator" style="width: 10px; height: 10px;"></span>
                    Live Transaction Stream
                </h2>
                <span class="text-gray-400 text-sm">Auto-updating</span>
            </div>
            
            @if($recentOrders->count() === 0)
                <div class="text-center py-16">
                    <div class="text-6xl mb-4 pulse-animation">📊</div>
                    <p class="text-gray-400 text-lg">Waiting for transactions...</p>
                    <p class="text-gray-500 text-sm mt-2">New orders will appear here in real-time</p>
                </div>
            @else
                <div class="space-y-4 max-h-screen overflow-y-auto">
                    @foreach($recentOrders as $order)
                        @php
                            $orderTime = $order->created_at;
                            $now = now();
                            $diffMinutes = $now->diffInMinutes($orderTime);
                            $timeAgo = $diffMinutes < 1 ? 'Just now' : 
                                       ($diffMinutes < 60 ? $diffMinutes . 'm ago' :
                                       floor($diffMinutes / 60) . 'h ago');
                            $isRecent = $diffMinutes < 5;
                        @endphp
                        
                        <div class="bg-gray-700 rounded-lg p-5 hover:bg-gray-650 transition border-l-4 
                                    {{ $isRecent ? 'border-green-500' : 
                                       ($order->status === 'completed' ? 'border-blue-500' : 
                                       ($order->status === 'preparing' ? 'border-orange-500' : 'border-gray-500')) }}">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-xl font-bold text-white">Order #{{ $order->order_id }}</h3>
                                        @if($isRecent)
                                            <span class="live-indicator" style="width: 8px; height: 8px;"></span>
                                        @endif
                                        <span class="px-3 py-1 text-xs font-bold rounded-full text-white 
                                            {{ $order->status === 'completed' ? 'bg-green-500' : 
                                               ($order->status === 'preparing' ? 'bg-orange-500' : 'bg-gray-500') }}">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-gray-300">
                                        <span class="flex items-center gap-1">
                                            <span>👤</span> {{ $order->user->username ?? 'Unknown' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span>🕒</span> {{ $timeAgo }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span>💳</span> {{ $order->payment_method }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold text-green-400">₱{{ number_format($order->total, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-600 pt-3 mt-3">
                                <p class="text-xs text-gray-400 mb-2 font-semibold">ORDER ITEMS:</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($order->orderItems as $item)
                                        <div class="bg-gray-800 rounded px-3 py-2 text-sm">
                                            <span class="text-white font-semibold">{{ $item->quantity }}×</span>
                                            <span class="text-gray-300"> {{ $item->menuItem->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-600 pt-3 mt-3 text-xs text-gray-400">
                                <p>Transaction Time: {{ $orderTime->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection