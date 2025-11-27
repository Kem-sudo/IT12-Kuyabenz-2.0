<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fix total sales calculation - only count completed orders
        $totalSales = Order::where('status', 'completed')->sum('total');
        
        // Fix today's orders count
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Fix active orders count
        $activeOrders = Order::whereIn('status', ['pending', 'preparing'])->count();
        
        $menuItems = MenuItem::count();
        
        $stats = [
            'totalSales' => $totalSales,
            'todayOrders' => $todayOrders,
            'activeOrders' => $activeOrders,
            'menuItems' => $menuItems,
        ];

        $recentOrders = Order::with(['user', 'orderItems.menuItem'])
                            ->latest()
                            ->take(10)
                            ->get();

        $users = User::all();
        $menuItems = MenuItem::all();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'users', 'menuItems'));
    }

    public function transactionMonitor()
    {
        $orders = Order::with(['user', 'orderItems.menuItem'])->get();
        
        $recentOrders = $orders->filter(function($order) {
            return $order->created_at >= now()->subDay();
        })->sortByDesc('created_at');

        $activeOrders = $orders->whereIn('status', ['pending', 'preparing'])->count();
        
        // Fix completed today calculation
        $completedToday = Order::where('status', 'completed')
                              ->whereDate('created_at', today())
                              ->count();
                              
        // Fix today sales calculation
        $todaySales = Order::where('status', 'completed')
                          ->whereDate('created_at', today())
                          ->sum('total');

        return view('admin.transaction-monitor', compact(
            'recentOrders', 'activeOrders', 'completedToday', 'todaySales'
        ));
    }

    public function salesReport(Request $request)
    {
        $filter = $request->get('filter', 'daily');
        
        // Base query for completed orders only
        $query = Order::where('status', 'completed');
        
        switch ($filter) {
            case 'weekly':
                $salesData = $query->selectRaw('
                    YEAR(created_at) as year, 
                    WEEK(created_at) as week, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->get()
                ->map(function($item) {
                    $item->date = "Week {$item->week}, {$item->year}";
                    return $item;
                });
                break;
                
            case 'monthly':
                $salesData = $query->selectRaw('
                    YEAR(created_at) as year, 
                    MONTH(created_at) as month, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
                ->map(function($item) {
                    $monthName = date('F', mktime(0, 0, 0, $item->month, 1));
                    $item->date = "{$monthName} {$item->year}";
                    return $item;
                });
                break;
                
            case 'yearly':
                $salesData = $query->selectRaw('
                    YEAR(created_at) as year, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get()
                ->map(function($item) {
                    $item->date = "Year {$item->year}";
                    return $item;
                });
                break;
                
            default: // daily
                $salesData = $query->selectRaw('
                    DATE(created_at) as date, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        }
        
        // Get all orders for the detailed table
        $orders = Order::with(['user', 'orderItems.menuItem'])
                      ->where('status', 'completed')
                      ->latest()
                      ->get();

        return view('admin.sales-report', compact('salesData', 'orders', 'filter'));
    }
    public function downloadSalesReport(Request $request)
{
    try {
        $filter = $request->get('filter', 'daily');
        $format = $request->get('format', 'csv');
        
        // Get sales data based on filter
        $query = Order::where('status', 'completed');
        
        switch ($filter) {
            case 'weekly':
                $salesData = $query->selectRaw('
                    YEAR(created_at) as year, 
                    WEEK(created_at) as week, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->get()
                ->map(function($item) {
                    $item->date = "Week {$item->week}, {$item->year}";
                    return $item;
                });
                break;
                
            case 'monthly':
                $salesData = $query->selectRaw('
                    YEAR(created_at) as year, 
                    MONTH(created_at) as month, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
                ->map(function($item) {
                    $monthName = date('F', mktime(0, 0, 0, $item->month, 1));
                    $item->date = "{$monthName} {$item->year}";
                    return $item;
                });
                break;
                
            case 'yearly':
                $salesData = $query->selectRaw('
                    YEAR(created_at) as year, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get()
                ->map(function($item) {
                    $item->date = "Year {$item->year}";
                    return $item;
                });
                break;
                
            default: // daily
                $salesData = $query->selectRaw('
                    DATE(created_at) as date, 
                    COUNT(*) as transactions, 
                    SUM(total) as total_sales
                ')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        }

        // Get all orders for detailed report
        $orders = Order::with(['user', 'orderItems.menuItem'])
                      ->where('status', 'completed')
                      ->latest()
                      ->get();

        $restaurantName = "Kuya Benz";
        $reportDate = now()->format('Y-m-d H:i:s');
        
        if ($format === 'csv') {
            return $this->downloadCSV($salesData, $orders, $filter, $restaurantName, $reportDate);
        } elseif ($format === 'pdf') {
            return $this->downloadPDF($salesData, $orders, $filter, $restaurantName, $reportDate);
        } else {
            return $this->downloadExcel($salesData, $orders, $filter, $restaurantName, $reportDate);
        }

    } catch (\Exception $e) {
        return redirect()->route('admin.sales')
                         ->with('download_error', 'Failed to generate report: ' . $e->getMessage());
    }
}

private function downloadCSV($salesData, $orders, $filter, $restaurantName, $reportDate)
{
    $fileName = "{$restaurantName}_Sales_Report_{$filter}_" . now()->format('Y-m-d') . ".csv";
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
    ];

    $callback = function() use ($salesData, $orders, $filter, $restaurantName, $reportDate) {
        $file = fopen('php://output', 'w');
        
        // Header
        fputcsv($file, ["{$restaurantName} - Sales Report"]);
        fputcsv($file, ["Report Type: " . ucfirst($filter)]);
        fputcsv($file, ["Generated: {$reportDate}"]);
        fputcsv($file, [""]);
        
        // Summary Section
        fputcsv($file, ["SUMMARY"]);
        fputcsv($file, ["Total Periods", count($salesData)]);
        fputcsv($file, ["Total Transactions", $salesData->sum('transactions')]);
        fputcsv($file, ["Total Sales", "₱" . number_format($salesData->sum('total_sales'), 2)]);
        fputcsv($file, ["Average per Period", "₱" . number_format($salesData->avg('total_sales') ?? 0, 2)]);
        fputcsv($file, [""]);
        
        // Sales Data Section
        fputcsv($file, ["SALES DATA BY PERIOD"]);
        fputcsv($file, ["Period", "Transactions", "Total Sales", "Average Sale"]);
        
        foreach ($salesData as $data) {
            $average = $data->transactions > 0 ? $data->total_sales / $data->transactions : 0;
            fputcsv($file, [
                $data->date,
                $data->transactions,
                "₱" . number_format($data->total_sales, 2),
                "₱" . number_format($average, 2)
            ]);
        }
        
        fputcsv($file, [""]);
        
        // Detailed Orders Section
        fputcsv($file, ["DETAILED ORDERS"]);
        fputcsv($file, ["Order ID", "Date", "Cashier", "Items Count", "Total Amount", "Status"]);
        
        foreach ($orders as $order) {
            fputcsv($file, [
                $order->order_id,
                $order->created_at->format('Y-m-d H:i'),
                $order->user->username,
                $order->orderItems->count(),
                "₱" . number_format($order->total, 2),
                ucfirst($order->status)
            ]);
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

private function downloadPDF($salesData, $orders, $filter, $restaurantName, $reportDate)
{
    // For PDF, we'll create a simple HTML version that can be printed as PDF
    $fileName = "{$restaurantName}_Sales_Report_{$filter}_" . now()->format('Y-m-d') . ".html";
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>{$restaurantName} - Sales Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
            .summary { background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .total-row { font-weight: bold; background-color: #e6f3ff; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>{$restaurantName}</h1>
            <h2>Sales Report - " . ucfirst($filter) . "</h2>
            <p>Generated: {$reportDate}</p>
        </div>
        
        <div class='summary'>
            <h3>Summary</h3>
            <p><strong>Total Periods:</strong> " . count($salesData) . "</p>
            <p><strong>Total Transactions:</strong> " . $salesData->sum('transactions') . "</p>
            <p><strong>Total Sales:</strong> ₱" . number_format($salesData->sum('total_sales'), 2) . "</p>
            <p><strong>Average per Period:</strong> ₱" . number_format($salesData->avg('total_sales') ?? 0, 2) . "</p>
        </div>
        
        <h3>Sales Data by Period</h3>
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Transactions</th>
                    <th>Total Sales</th>
                    <th>Average Sale</th>
                </tr>
            </thead>
            <tbody>";
    
    $totalTransactions = 0;
    $totalSales = 0;
    
    foreach ($salesData as $data) {
        $average = $data->transactions > 0 ? $data->total_sales / $data->transactions : 0;
        $totalTransactions += $data->transactions;
        $totalSales += $data->total_sales;
        
        $html .= "
                <tr>
                    <td>{$data->date}</td>
                    <td>{$data->transactions}</td>
                    <td>₱" . number_format($data->total_sales, 2) . "</td>
                    <td>₱" . number_format($average, 2) . "</td>
                </tr>";
    }
    
    $overallAverage = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
    
    $html .= "
                <tr class='total-row'>
                    <td><strong>TOTAL</strong></td>
                    <td><strong>{$totalTransactions}</strong></td>
                    <td><strong>₱" . number_format($totalSales, 2) . "</strong></td>
                    <td><strong>₱" . number_format($overallAverage, 2) . "</strong></td>
                </tr>
            </tbody>
        </table>
        
        <h3>Recent Orders ({$orders->count()} total)</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Cashier</th>
                    <th>Items</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($orders as $order) {
        $html .= "
                <tr>
                    <td>{$order->order_id}</td>
                    <td>{$order->created_at->format('M d, Y H:i')}</td>
                    <td>{$order->user->username}</td>
                    <td>{$order->orderItems->count()}</td>
                    <td>₱" . number_format($order->total, 2) . "</td>
                </tr>";
    }
    
    $html .= "
            </tbody>
        </table>
        
        <div style='margin-top: 30px; text-align: center; color: #666;'>
            <p>Report generated by Kuya Benz POS System</p>
        </div>
    </body>
    </html>";
    
    return response($html)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
}

private function downloadExcel($salesData, $orders, $filter, $restaurantName, $reportDate)
{
    // For Excel, we'll use CSV format which Excel can open
    return $this->downloadCSV($salesData, $orders, $filter, $restaurantName, $reportDate);
}
}