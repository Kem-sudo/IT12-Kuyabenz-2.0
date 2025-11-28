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
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-lg"></span>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.transactions') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-lg"></span>
                <span>Live Monitor</span>
            </a>
            
            <a href="{{ route('admin.menu') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-lg"></span>
                <span>Menu</span>
            </a>
            
            <a href="{{ route('admin.sales') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition bg-gray-700 font-bold">
                <span class="text-lg"></span>
                <span>Sales Report</span>
            </a>
            
            <a href="{{ route('admin.users') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-gray-700">
                <span class="text-lg"></span>
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
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Sales Report</h2>
                <div class="flex gap-2">
                    <a href="{{ route('admin.sales') }}?filter=daily" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'daily' ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Daily
                    </a>
                    <a href="{{ route('admin.sales') }}?filter=weekly" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'weekly' ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Weekly
                    </a>
                    <a href="{{ route('admin.sales') }}?filter=monthly" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'monthly' ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Monthly
                    </a>
                    <a href="{{ route('admin.sales') }}?filter=yearly" class="px-4 py-2 rounded-lg font-semibold transition {{ $filter === 'yearly' ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                        Yearly
                    </a>
                </div>
            </div>
            
            <!-- Download Format Selection -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Export Options</h3>
                <div class="flex gap-4">
                    <button onclick="downloadReport('csv')" class="px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition flex items-center gap-2">
                         Download CSV
                    </button>
                    <button onclick="downloadReport('pdf')" class="px-6 py-3 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-600 transition flex items-center gap-2">
                         Download PDF
                    </button>
                    <button onclick="downloadReport('excel')" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-500 transition flex items-center gap-2">
                         Download Excel
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">Choose your preferred format for the sales report</p>
            </div>
            
            <!-- Sales Summary -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Sales Summary - {{ ucfirst($filter) }} Report</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($salesData->sum('total_sales'), 2) }}</p>
                        <p class="text-sm text-gray-600">Total Sales</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-2xl font-bold text-gray-800">{{ $salesData->sum('transactions') }}</p>
                        <p class="text-sm text-gray-600">Total Transactions</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-2xl font-bold text-gray-800">{{ $salesData->count() }}</p>
                        <p class="text-sm text-gray-600">Periods</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($salesData->avg('total_sales') ?? 0, 2) }}</p>
                        <p class="text-sm text-gray-600">Average per Period</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 px-4 font-bold text-gray-800">Period</th>
                                <th class="text-center py-3 px-4 font-bold text-gray-800">Transactions</th>
                                <th class="text-right py-3 px-4 font-bold text-gray-800">Total Sales</th>
                                <th class="text-right py-3 px-4 font-bold text-gray-800">Average Sale</th>
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
                                        <td class="py-3 px-4 font-medium text-gray-800">
                                            {{ $data->date ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-center text-gray-700">{{ $data->transactions }}</td>
                                        <td class="py-3 px-4 text-right font-bold text-gray-800">₱{{ number_format($data->total_sales, 2) }}</td>
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
                            <tr class="border-t-2 border-gray-300 font-bold">
                                <td class="py-4 px-4 text-gray-800">TOTAL</td>
                                <td class="py-4 px-4 text-center text-gray-800">{{ $totalTransactions }}</td>
                                <td class="py-4 px-4 text-right text-xl text-gray-800">₱{{ number_format($totalSales, 2) }}</td>
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
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Recent Completed Orders</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 px-4 font-bold text-gray-800">Order ID</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-800">Date & Time</th>
                                <th class="text-left py-3 px-4 font-bold text-gray-800">Cashier</th>
                                <th class="text-center py-3 px-4 font-bold text-gray-800">Items</th>
                                <th class="text-right py-3 px-4 font-bold text-gray-800">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($orders->count() > 0)
                                @foreach($orders as $order)
                                    <tr class="border-b hover:bg-gray-50 transition">
                                        <td class="py-3 px-4 font-medium text-gray-800">#{{ $order->order_id }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-700">{{ $order->user->username }}</td>
                                        <td class="py-3 px-4 text-center text-gray-700">{{ $order->orderItems->count() }}</td>
                                        <td class="py-3 px-4 text-right font-bold text-gray-800">₱{{ number_format($order->total, 2) }}</td>
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
    <div class="bg-white rounded-lg border border-gray-200 p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Downloading Report</h3>
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600 mx-auto mb-4"></div>
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