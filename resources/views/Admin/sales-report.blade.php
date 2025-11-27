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
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-xl">📊</span>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.transactions') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-xl">📡</span>
                <span>Live Monitor</span>
            </a>
            
            <a href="{{ route('admin.menu') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-xl">🍽️</span>
                <span>Menu</span>
            </a>
            
            <a href="{{ route('admin.sales') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition bg-white bg-opacity-20 font-bold">
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
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold" style="color: #1f2937;">Sales Report</h2>
                <div class="flex gap-2">
                
                    <a href="{{ route('admin.sales') }}?filter=daily" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'daily' ? 'bg-red-600' : 'bg-gray-200 text-gray-800' }}">
                        Daily
                    </a>
                    <a href="{{ route('admin.sales') }}?filter=weekly" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'weekly' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                        Weekly
                    </a>
                    <a href="{{ route('admin.sales') }}?filter=monthly" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'monthly' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                        Monthly
                    </a>
                    <a href="{{ route('admin.sales') }}?filter=yearly" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'yearly' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                        Yearly
                    </a>
                </div>
            </div>
            
            <!-- Download Format Selection -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4" style="color: #1f2937;">Export Options</h3>
                <div class="flex gap-4">
                    <button onclick="downloadReport('csv')" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition flex items-center gap-2">
                        📊 Download CSV
                    </button>
                    <button onclick="downloadReport('pdf')" class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition flex items-center gap-2">
                        📄 Download PDF
                    </button>
                    <button onclick="downloadReport('excel')" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition flex items-center gap-2">
                        📈 Download Excel
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">Choose your preferred format for the sales report</p>
            </div>
            
            <!-- Sales Summary -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold mb-4" style="color: #1f2937;">Sales Summary - {{ ucfirst($filter) }} Report</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-2xl font-bold text-green-600">₱{{ number_format($salesData->sum('total_sales'), 2) }}</p>
                        <p class="text-sm text-gray-600">Total Sales</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-2xl font-bold text-blue-600">{{ $salesData->sum('transactions') }}</p>
                        <p class="text-sm text-gray-600">Total Transactions</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-2xl font-bold text-purple-600">{{ $salesData->count() }}</p>
                        <p class="text-sm text-gray-600">Periods</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <p class="text-2xl font-bold text-orange-600">₱{{ number_format($salesData->avg('total_sales') ?? 0, 2) }}</p>
                        <p class="text-sm text-gray-600">Average per Period</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2" style="border-color: #dc2626;">
                                <th class="text-left py-3 px-4 font-bold" style="color: #1f2937;">Period</th>
                                <th class="text-center py-3 px-4 font-bold" style="color: #1f2937;">Transactions</th>
                                <th class="text-right py-3 px-4 font-bold" style="color: #1f2937;">Total Sales</th>
                                <th class="text-right py-3 px-4 font-bold" style="color: #1f2937;">Average Sale</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalTransactions = 0;
                                $totalSales = 0;
                            @endphp
                            @if($salesData->count() > 0)
                                @foreach($salesData as $data)
                                    @php
                                        $totalTransactions += $data->transactions;
                                        $totalSales += $data->total_sales;
                                        $average = $data->transactions > 0 ? $data->total_sales / $data->transactions : 0;
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="py-3 px-4 font-medium" style="color: #1f2937;">
                                            {{ $data->date ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">{{ $data->transactions }}</td>
                                        <td class="py-3 px-4 text-right font-bold" style="color: #dc2626;">₱{{ number_format($data->total_sales, 2) }}</td>
                                        <td class="py-3 px-4 text-right text-gray-600">₱{{ number_format($average, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">No sales data available</td>
                                </tr>
                            @endif
                        </tbody>
                        @if($salesData->count() > 0)
                        <tfoot>
                            <tr class="border-t-2 font-bold" style="border-color: #dc2626;">
                                <td class="py-4 px-4" style="color: #1f2937;">TOTAL</td>
                                <td class="py-4 px-4 text-center" style="color: #1f2937;">{{ $totalTransactions }}</td>
                                <td class="py-4 px-4 text-right text-xl" style="color: #dc2626;">₱{{ number_format($totalSales, 2) }}</td>
                                <td class="py-4 px-4 text-right text-gray-600">
                                    ₱{{ number_format($totalTransactions > 0 ? $totalSales / $totalTransactions : 0, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold mb-4" style="color: #1f2937;">Recent Completed Orders</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2" style="border-color: #dc2626;">
                                <th class="text-left py-3 px-4 font-bold" style="color: #1f2937;">Order ID</th>
                                <th class="text-left py-3 px-4 font-bold" style="color: #1f2937;">Date & Time</th>
                                <th class="text-left py-3 px-4 font-bold" style="color: #1f2937;">Cashier</th>
                                <th class="text-center py-3 px-4 font-bold" style="color: #1f2937;">Items</th>
                                <th class="text-right py-3 px-4 font-bold" style="color: #1f2937;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($orders->count() > 0)
                                @foreach($orders as $order)
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="py-3 px-4 font-medium">#{{ $order->order_id }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $order->user->username }}</td>
                                        <td class="py-3 px-4 text-center">{{ $order->orderItems->count() }}</td>
                                        <td class="py-3 px-4 text-right font-bold" style="color: #dc2626;">₱{{ number_format($order->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">No completed orders yet</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Download Modal -->
<div id="downloadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4" style="color: #1f2937;">Downloading Report</h3>
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Preparing your {{ strtoupper($filter) }} sales report...</p>
            <p class="text-sm text-gray-500 mt-2">This may take a few moments</p>
        </div>
    </div>
</div>

<script>
    function downloadReport(format) {
        // Show loading modal
        document.getElementById('downloadModal').classList.remove('hidden');
        
        // Set the format and submit the form
        document.getElementById('downloadFormat').value = format;
        
        // Submit the form after a short delay to show the loading animation
        setTimeout(() => {
            document.getElementById('downloadForm').submit();
        }, 1000);
    }

    // Close modal if user clicks outside
    document.getElementById('downloadModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Show success message if download was successful
    @if(session('download_success'))
        alert('{{ session('download_success') }}');
    @endif

    @if(session('download_error'))
        alert('Error: {{ session('download_error') }}');
        document.getElementById('downloadModal').classList.add('hidden');
    @endif
</script>
@endsection