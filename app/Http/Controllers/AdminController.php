<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AuditLogger;


class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalSales'   => Order::where('status', 'completed')->sum('total'),
            'todayOrders'  => Order::whereDate('created_at', today())->count(),
            'activeOrders' => Order::whereIn('status', ['pending', 'preparing'])->count(),
            'menuItems'    => MenuItem::count(),
        ];

        $recentOrders = Order::with(['user', 'orderItems.menuItem'])
            ->latest()
            ->take(10)
            ->get();

        $users = User::all();
        $menuItems = MenuItem::all();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'users',
            'menuItems'
        ));
    }

    public function transactionMonitor()
    {
        $recentOrders = Order::with(['user', 'orderItems.menuItem'])
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->get();

        $activeOrders = Order::whereIn('status', ['pending', 'preparing'])->count();

        $completedToday = Order::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        $todaySales = Order::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total');

        Log::info('Transaction Monitor Loaded');

        return view('admin.transaction-monitor', compact(
            'recentOrders',
            'activeOrders',
            'completedToday',
            'todaySales'
        ));
    }

    public function salesReport(Request $request)
{
    $from = $request->from;
    $to = $request->to;

    $query = Order::with(['user', 'orderItems'])
        ->where('status', 'completed');

    if ($from) {
        $query->whereDate('created_at', '>=', $from);
    }

    if ($to) {
        $query->whereDate('created_at', '<=', $to);
    }

    $orders = $query->latest()->get();

    $salesData = $query->selectRaw("
        DATE(created_at) as date,
        COUNT(*) as transactions,
        SUM(total) as total_sales
    ")
    ->groupBy('date')
    ->orderBy('date', 'desc')
    ->get();

    AuditLogger::log('admin.sales_report.viewed', [
        'from' => $from,
        'to' => $to,
    ], $request);

    return view('admin.sales-report', compact(
        'orders',
        'salesData',
        'from',
        'to'
    ));
}

    public function getRealTimeSales(Request $request)
    {
        try {
            $filter = $request->get('filter', 'daily');
            $selectedDate = $request->get('selected_date');

            $salesData = $this->getSalesData($filter, $selectedDate);
            $orders = $this->getOrders($selectedDate);

            return response()->json([
                'success' => true,
                'salesData' => $salesData,
                'orders' => $orders,
                'summary' => [
                    'totalPeriods' => $salesData->count(),
                    'totalTransactions' => $salesData->sum('transactions'),
                    'totalSales' => number_format($salesData->sum('total_sales'), 2),
                ],
                'lastUpdated' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sales data'
            ], 500);
        }
    }

    private function getSalesData($filter, $selectedDate = null)
    {
        $query = Order::where('status', 'completed');

        if ($selectedDate) {
            return $query->whereDate('created_at', $selectedDate)
                ->selectRaw("
                    DATE(created_at) as date,
                    COUNT(*) as transactions,
                    SUM(total) as total_sales
                ")
                ->groupBy('date')
                ->get();
        }

        switch ($filter) {

            case 'weekly':
                return $query->selectRaw("
                        YEAR(created_at) as year,
                        WEEK(created_at) as week,
                        COUNT(*) as transactions,
                        SUM(total) as total_sales
                    ")
                    ->groupBy('year', 'week')
                    ->get()
                    ->map(function ($row) {
                        $row->date = 'Week ' . $row->week . ', ' . $row->year;
                        return $row;
                    });

            case 'monthly':
                return $query->selectRaw("
                        YEAR(created_at) as year,
                        MONTH(created_at) as month,
                        COUNT(*) as transactions,
                        SUM(total) as total_sales
                    ")
                    ->groupBy('year', 'month')
                    ->get()
                    ->map(function ($row) {
                        $row->date = date('F', mktime(0, 0, 0, $row->month, 1)) . ' ' . $row->year;
                        return $row;
                    });

            case 'yearly':
                return $query->selectRaw("
                        YEAR(created_at) as year,
                        COUNT(*) as transactions,
                        SUM(total) as total_sales
                    ")
                    ->groupBy('year')
                    ->get()
                    ->map(function ($row) {
                        $row->date = 'Year ' . $row->year;
                        return $row;
                    });

            default:
                return $query->selectRaw("
                        DATE(created_at) as date,
                        COUNT(*) as transactions,
                        SUM(total) as total_sales
                    ")
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();
        }
    }

    private function getOrders($selectedDate = null)
    {
        $query = Order::with(['user', 'orderItems.menuItem'])
            ->where('status', 'completed');

        if ($selectedDate) {
            $query->whereDate('created_at', $selectedDate);
        }

        return $query->latest()->get();
    }

    public function downloadSalesReport(Request $request)
{
    $from = $request->from;
    $to = $request->to;

    $selectedDate = null; // FIX undefined variable

    $query = Order::with(['user', 'orderItems'])
        ->where('status', 'completed');

    if ($from) {
        $query->whereDate('created_at', '>=', $from);
    }

    if ($to) {
        $query->whereDate('created_at', '<=', $to);
    }

    $orders = $query->latest()->get();

    $salesData = (clone $query)
        ->selectRaw("
            DATE(created_at) as date,
            COUNT(*) as transactions,
            SUM(total) as total_sales
        ")
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();

    $data = [
        'salesData' => $salesData,
        'orders' => $orders,
        'from' => $from,
        'to' => $to,
        'selectedDate' => $selectedDate,
        'reportDate' => now(),
        'generatedBy' => auth()->user()->username,
    ];

    AuditLogger::log('admin.sales_report.downloaded', [
        'from' => $from,
        'to' => $to,
        'orders_count' => $orders->count(),
        'total_sales' => (float) $orders->sum('total'),
    ], $request);

    $pdf = Pdf::loadView('admin.reports.sales-pdf', $data)
        ->setPaper('A4', 'portrait');

    return $pdf->download('sales-report.pdf');
}
}