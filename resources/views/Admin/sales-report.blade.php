@extends('layouts.App')

@section('content')
<div class="w-full flex min-h-screen">

    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white shadow-lg flex flex-col h-screen sticky top-0">

        <!-- Header -->
        <div class="p-6 border-b border-gray-700 flex-shrink-0">
            <h1 class="text-2xl font-bold mb-1">Kuya Benz</h1>
            <p class="text-sm text-gray-300">Admin Panel</p>
        </div>

        <!-- Menu -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
                Dashboard
            </a>

            <a href="{{ route('admin.transactions') }}"
               class="block px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
                Live Monitor
            </a>

            <a href="{{ route('admin.menu') }}"
               class="block px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
                Menu
            </a>

            <a href="{{ route('admin.sales') }}"
               class="block px-4 py-3 rounded-lg mb-2 bg-gray-700 font-bold">
                Sales Report
            </a>

            <a href="{{ route('admin.users') }}"
               class="block px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
                Staff
            </a>
        </nav>

        <!-- Logout Always Visible -->
        <div class="p-4 border-t border-gray-700 flex-shrink-0">
            <div class="mb-4 p-3 bg-gray-700 rounded-lg">
                <p class="text-xs text-gray-300 mb-1">Logged in as</p>
                <p class="font-bold">{{ auth()->user()->username }}</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-600 transition">
                    Logout
                </button>
            </form>
        </div>

    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-gray-50 p-6 overflow-y-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Sales Report</h2>

            <form method="GET" action="{{ route('admin.sales') }}" class="flex gap-3">

<div>
<label class="text-sm text-gray-600">From</label>
<input type="date" name="from" value="{{ request('from') }}"
       class="border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
</div>

<div>
<label class="text-sm text-gray-600">To</label>
<input type="date" name="to" value="{{ request('to') }}"
       class="border px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
</div>

<button type="submit"
        class="px-5 py-2 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition">
    Filter
</button>

<a href="{{ route('admin.sales') }}"
   class="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition">
    Reset
</a>

</form>
        </div>

        <!-- Export -->
        <div class="bg-white rounded-lg border shadow-sm p-6 mb-6 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Export Report</h3>
                <p class="text-sm text-gray-500">Download sales report as PDF</p>
            </div>

            <button onclick="downloadReport()"
                class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                Download PDF
            </button>
        </div>

        <!-- Hidden Form -->
        <form id="downloadForm" method="POST" action="{{ route('admin.sales.download') }}" class="hidden">
            @csrf
            <input type="hidden" name="from" value="{{ request('from') }}">
            <input type="hidden" name="to" value="{{ request('to') }}">
        </form>

    
        <!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <!-- Total Sales -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">
    Total Sales
</h3>

        <p class="text-3xl font-bold text-gray-800 text-right">
            ₱{{ number_format($salesData->sum('total_sales'), 2) }}
        </p>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">
            Transactions
        </h3>

        <p class="text-3xl font-bold text-gray-800 text-right">
            {{ $salesData->sum('transactions') }}
        </p>
    </div>

    <!-- Periods -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">
            Periods
        </h3>

        <p class="text-3xl font-bold text-gray-800 text-right">
            {{ $salesData->count() }}
        </p>
    </div>

</div>

    
        <!-- Sales Table + Recent Orders Wrapper -->
<div class="space-y-6">

    <!-- Sales Table Title -->
    <h3 class="text-xl font-bold text-gray-800">
        @if(request('from') || request('to'))
            Filtered Sales Summary
        @else
            Daily Sales Summary
        @endif
    </h3>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg border shadow-sm p-6">
        <div class="overflow-x-auto">
            <table class="w-full">

                    <thead>
                        <tr class="border-b-2">
                            <th class="text-left py-3 px-4">Period</th>
                            <th class="text-center py-3 px-4">Transactions</th>
                            <th class="text-right py-3 px-4">Sales</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $totalTransactions = 0;
                            $totalSales = 0;
                        @endphp

                        @forelse($salesData as $data)
                            @php
                                $totalTransactions += $data->transactions;
                                $totalSales += $data->total_sales;
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $data->date }}</td>
                                <td class="py-3 px-4 text-center">{{ $data->transactions }}</td>
                                <td class="py-3 px-4 text-right font-semibold">
                                    ₱{{ number_format($data->total_sales, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-gray-500">
                                    No sales data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($salesData->count())
                    <tfoot>
                        <tr class="border-t-2 font-bold">
                            <td class="py-4 px-4">TOTAL</td>
                            <td class="py-4 px-4 text-center">{{ $totalTransactions }}</td>
                            <td class="py-4 px-4 text-right">
                                ₱{{ number_format($totalSales, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif

                </table>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg border shadow-sm p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Recent Completed Orders</h3>

            <div class="overflow-x-auto">
                <table class="w-full">

                    <thead>
                        <tr class="border-b-2">
                            <th class="text-left py-3 px-4">Order ID</th>
                            <th class="text-left py-3 px-4">Date</th>
                            <th class="text-left py-3 px-4">Cashier</th>
                            <th class="text-center py-3 px-4">Items</th>
                            <th class="text-right py-3 px-4">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">#{{ $order->order_id }}</td>
                                <td class="py-3 px-4">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                <td class="py-3 px-4">{{ $order->user->username }}</td>
                                <td class="py-3 px-4 text-center">{{ $order->orderItems->count() }}</td>
                                <td class="py-3 px-4 text-right font-semibold">
                                    ₱{{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-500">
                                    No completed orders yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div id="downloadModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg p-6 w-80 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-800 mx-auto mb-4"></div>
        <h3 class="text-lg font-bold">Preparing Report...</h3>
        <p class="text-sm text-gray-500 mt-2">Please wait</p>
    </div>

</div>

<script>
function downloadReport() {
    document.getElementById('downloadModal').classList.remove('hidden');
    document.getElementById('downloadModal').classList.add('flex');

    document.getElementById('downloadForm').submit();

    setTimeout(() => {
        document.getElementById('downloadModal').classList.add('hidden');
        document.getElementById('downloadModal').classList.remove('flex');
    }, 2000);
}
</script>

@endsection