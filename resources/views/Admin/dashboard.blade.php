@extends('layouts.app')

@section('content')
<div class="w-full h-full flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white shadow-lg flex flex-col">
        <div class="p-6 border-b border-gray-700">
            <h1 class="text-2xl font-bold mb-1">Kuya Benz</h1>
            <p class="text-sm text-gray-300">Admin Panel</p>
        </div>
        
        <nav class="flex-1 p-4">
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition bg-gray-700 font-bold">
                <span class="text-xl"></span>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.transactions') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-xl"></span>
                <span>Live Monitor</span>
            </a>
            
            <a href="{{ route('admin.menu') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-xl"></span>
                <span>Menu</span>
            </a>
            
            <a href="{{ route('admin.sales') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-xl"></span>
                <span>Sales Report</span>
            </a>
            
            <a href="{{ route('admin.users') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-xl"></span>
                <span>Staff</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-700">
            <div class="mb-4 p-3 bg-gray-700 rounded-lg">
                <p class="text-xs text-gray-300 mb-1">Logged in as</p>
                <p class="font-bold">{{ auth()->user()->username }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-gray-700 text-center py-3 rounded-lg font-semibold hover:bg-gray-600 transition text-white">
                    Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 bg-gray-50">
        <div class="p-6">
            <h2 class="text-3xl font-bold mb-6 text-gray-800">Dashboard Overview</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Total Sales</h3>
                    <p class="text-3xl font-bold text-gray-800">₱{{ number_format($stats['totalSales'], 2) }}</p>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Today's Orders</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['todayOrders'] }}</p>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Active Orders</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['activeOrders'] }}</p>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Menu Items</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['menuItems'] }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.menu') }}" class="p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 transition text-left">
                        <div class="text-2xl mb-2"></div>
                        <div class="font-semibold text-gray-800">Add Menu Item</div>
                        <div class="text-sm text-gray-600">Add new item to menu</div>
                    </a>
                    
                    <a href="{{ route('admin.users') }}" class="p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 transition text-left">
                        <div class="text-2xl mb-2"></div>
                        <div class="font-semibold text-gray-800">Add Staff</div>
                        <div class="text-sm text-gray-600">Create staff account</div>
                    </a>
                    
                    <a href="{{ route('admin.transactions') }}" class="p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 transition text-left">
                        <div class="text-2xl mb-2"></div>
                        <div class="font-semibold text-gray-800">Live Monitor</div>
                        <div class="text-sm text-gray-600">Real-time transactions</div>
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-800">Recent Orders</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @if($recentOrders->count() === 0)
                            <p class="text-gray-500 text-center py-8">No orders yet</p>
                        @else
                            @foreach($recentOrders as $order)
                                <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="font-semibold text-gray-800">Order #{{ $order->order_id }}</span>
                                            <p class="text-xs text-gray-600">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-semibold rounded 
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($order->status === 'preparing' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $order->orderItems->count() }} item(s) | {{ $order->payment_method }}</p>
                                    <p class="font-bold text-gray-800">₱{{ number_format($order->total, 2) }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-800">Staff Management</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @if($users->count() === 0)
                            <p class="text-gray-500 text-center py-8">No staff accounts yet</p>
                        @else
                            @foreach($users as $user)
                                <div class="flex justify-between items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">{{ $user->username }}</h4>
                                        <p class="text-sm text-gray-600">Role: <strong>{{ $user->role }}</strong></p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 text-xs font-semibold rounded 
                                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                               ($user->role === 'cashier' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
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
                        <a href="{{ route('admin.users') }}" class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold text-center block hover:bg-gray-700 transition">
                            Manage Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection