<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock_status', 'low_stock')->count(),
            'total_customers' => Customer::count(),
            'total_revenue' => Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])->sum('total_amount'),
        ];

        // Órdenes del día
        $todayOrders = Order::whereDate('created_at', today())
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Ventas de los últimos 7 días
        $salesData = Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->orderBy('date')
            ->get();

        // Productos más vendidos
        $topProducts = Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit(5)
            ->get();

        // Productos con bajo stock
        $lowStockProducts = Product::where('stock_status', 'low_stock')
            ->orWhere('stock', '<=', DB::raw('min_stock'))
            ->orderBy('stock')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'todayOrders',
            'salesData',
            'topProducts',
            'lowStockProducts'
        ));
    }

    public function salesReport()
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])->sum('total_amount'),
            'average_order' => $orders->count() > 0 ? $orders->avg('total_amount') : 0,
            'total_products' => $orders->sum(function ($order) {
                return $order->items->sum('quantity');
            }),
        ];

        return view('admin.reports.sales', compact('orders', 'summary', 'startDate', 'endDate'));
    }

    public function productsReport()
    {
        $products = Product::with(['category', 'brand'])
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->get();

        return view('admin.reports.products', compact('products'));
    }

    public function customersReport()
    {
        $customers = Customer::withCount('orders')
            ->with(['orders' => function ($query) {
                $query->select('customer_id', DB::raw('SUM(total_amount) as total_spent'))
                    ->groupBy('customer_id');
            }])
            ->orderBy('orders_count', 'desc')
            ->get();

        return view('admin.reports.customers', compact('customers'));
    }
}
