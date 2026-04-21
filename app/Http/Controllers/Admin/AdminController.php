<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintJob;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $paidStatuses = ['confirmed', 'processing', 'shipped', 'delivered'];

        $stats = [
            'total_users'          => User::where('role', '!=', 'admin')->count(),
            'pending_users'        => User::where('role', 'pending')->count(),
            'approved_users'       => User::where('role', 'approved')->count(),
            'total_products'       => Product::count(),
            'active_products'      => Product::where('is_active', true)->count(),
            'low_stock_products'   => Product::lowStock()->count(),
            'total_orders'         => Order::count(),
            'orders_pending'       => Order::where('status', 'pending')->count(),
            'orders_processing'    => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'orders_shipped'       => Order::where('status', 'shipped')->count(),
            'total_quotations'     => Quotation::count(),
            'quotations_pending'   => Quotation::where('status', 'pending')->count(),
            'quotations_reviewing' => Quotation::where('status', 'reviewing')->count(),
            'quotations_quoted'    => Quotation::where('status', 'quoted')->count(),
            'revenue_total'        => Order::whereIn('status', $paidStatuses)->sum('total'),
            'revenue_month'        => Order::whereIn('status', $paidStatuses)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
            'revenue_today'        => Order::whereIn('status', $paidStatuses)
                ->whereDate('created_at', today())
                ->sum('total'),
            // Print jobs
            'print_jobs_ordered'       => PrintJob::where('status', 'ordered')->count(),
            'print_jobs_in_production' => PrintJob::where('status', 'in_production')->count(),
            'print_jobs_completed'     => PrintJob::where('status', 'completed')->count(),
            'print_jobs_no_artwork'    => PrintJob::whereIn('status', ['ordered', 'in_production'])
                ->whereNull('artwork_path')->count(),
        ];

        // Monthly revenue — last 6 months
        $monthlyRevenue = collect(range(5, 0))->map(function ($monthsAgo) use ($paidStatuses) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month'   => $date->translatedFormat('M'),
                'year'    => $date->year,
                'revenue' => (float) Order::whereIn('status', $paidStatuses)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('total'),
            ];
        });

        // Top 5 products by revenue (from order items)
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(total) as revenue'), DB::raw('SUM(quantity) as units_sold'))
            ->whereHas('order', fn($q) => $q->whereIn('status', $paidStatuses))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->with('product:id,name,sku')
            ->take(5)
            ->get();

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        $recentQuotations = Quotation::with('user')
            ->latest()
            ->take(8)
            ->get();

        $pendingUsers = User::where('role', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyRevenue',
            'topProducts',
            'recentOrders',
            'recentQuotations',
            'pendingUsers'
        ));
    }
}
