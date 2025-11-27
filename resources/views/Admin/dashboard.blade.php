@extends('layouts.app')

@section('content')
<div class="w-full h-full flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="w-64 text-white shadow-2xl flex flex-col" style="background: linear-gradient(180deg, #dc2626 0%, #f59e0b 100%);">
        <div class="p-6 border-b border-white border-opacity-20">
            <h1 class="text-2xl font-bold mb-1">Kuya Benz</h1>
            <p class="text-sm opacity-90">Admin Panel</p>
        </div>
        
        <nav class="flex-1 p-4">
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition bg-white bg-opacity-20 font-bold">
                <span class="text-xl">📊</span>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.transactions') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10 relative">
                <span class="text-xl">📡</span>
                <span>Live Monitor</span>
            </a>
            
            <a href="{{ route('admin.menu') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-xl">🍽️</span>
                <span>Menu</span>
            </a>
            
            <a href="{{ route('admin.sales') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-xl">💰</span>
                <span>Sales Report</span>
            </a>
            
            <a href="{{ route('admin.users') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-xl">👥</span>
                <span>Staff</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-white border-opacity-20">
            <div class="mb-4 p-3 bg-white bg-opacity-10 rounded-lg">
                <p class="text-xs opacity-75 mb-1">Logged in as</p>
                <p class="font-bold">{{ auth()->user()->username }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-white text-center py-3 rounded-lg font-semibold hover:bg-opacity-90 transition" style="color: #dc2626;">
                    Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1" style="background-color: #f3f4f6;">
        <div class="p-6">
            <h2 class="text-3xl font-bold mb-6" style="color: #1f2937;">Dashboard Overview</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-sm font-medium opacity-90 mb-2">Total Sales</h3>
                    <p class="text-4xl font-bold">₱{{ number_format($stats['totalSales'], 2) }}</p>
                </div>
                
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-sm font-medium opacity-90 mb-2">Today's Orders</h3>
                    <p class="text-4xl font-bold">{{ $stats['todayOrders'] }}</p>
                </div>
                
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-sm font-medium opacity-90 mb-2">Active Orders</h3>
                    <p class="text-4xl font-bold">{{ $stats['activeOrders'] }}</p>
                </div>
                
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-sm font-medium opacity-90 mb-2">Menu Items</h3>
                    <p class="text-4xl font-bold">{{ $stats['menuItems'] }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4" style="color: #1f2937;">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.menu') }}" class="p-4 rounded-lg border-2 border-dashed hover:border-solid transition text-left" style="border-color: #dc2626; color: #1f2937;">
                        <div class="text-3xl mb-2">➕</div>
                        <div class="font-semibold">Add Menu Item</div>
                        <div class="text-sm text-gray-600">Add new item to menu</div>
                    </a>
                    
                    <a href="{{ route('admin.users') }}" class="p-4 rounded-lg border-2 border-dashed hover:border-solid transition text-left" style="border-color: #dc2626; color: #1f2937;">
                        <div class="text-3xl mb-2">👤</div>
                        <div class="font-semibold">Add Staff</div>
                        <div class="text-sm text-gray-600">Create staff account</div>
                    </a>
                    
                    <a href="{{ route('admin.transactions') }}" class="p-4 rounded-lg border-2 border-dashed hover:border-solid transition text-left" style="border-color: #dc2626; color: #1f2937;">
                        <div class="text-3xl mb-2">📡</div>
                        <div class="font-semibold">Live Monitor</div>
                        <div class="text-sm text-gray-600">Real-time transactions</div>
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4" style="color: #1f2937;">Recent Orders</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @if($recentOrders->count() === 0)
                            <p class="text-gray-500 text-center py-8">No orders yet</p>
                        @else
                            @foreach($recentOrders as $order)
                                <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="font-semibold" style="color: #1f2937;">Order #{{ $order->order_id }}</span>
                                            <p class="text-xs text-gray-600">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-semibold rounded text-white 
                                            {{ $order->status === 'completed' ? 'bg-green-500' : 
                                               ($order->status === 'preparing' ? 'bg-orange-500' : 'bg-gray-500') }}">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $order->orderItems->count() }} item(s) | {{ $order->payment_method }}</p>
                                    <p class="font-bold" style="color: #dc2626;">₱{{ number_format($order->total, 2) }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4" style="color: #1f2937;">Staff Management</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @if($users->count() === 0)
                            <p class="text-gray-500 text-center py-8">No staff accounts yet</p>
                        @else
                            @foreach($users as $user)
                                <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                                    <div class="flex-1">
                                        <h4 class="font-semibold" style="color: #1f2937;">{{ $user->username }}</h4>
                                        <p class="text-sm text-gray-600">Role: <strong>{{ $user->role }}</strong></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 text-xs font-semibold rounded text-white 
                                            {{ $user->role === 'admin' ? 'bg-red-600' : 
                                               ($user->role === 'cashier' ? 'bg-orange-500' : 'bg-gray-600') }}">
                                            {{ strtoupper($user->role) }}
                                        </span>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700" 
                                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-500">(You)</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users') }}" class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold text-center block hover:bg-red-700 transition">
                            Manage Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection