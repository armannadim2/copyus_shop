<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintJob;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    private function yearExpr(string $col): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%Y', $col) AS INTEGER)"
            : "YEAR($col)";
    }

    private function monthExpr(string $col): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', $col) AS INTEGER)"
            : "MONTH($col)";
    }

    private function dateDiffExpr(string $a, string $b): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "julianday($a) - julianday($b)"
            : "DATEDIFF($a, $b)";
    }

    private function jsonNameExpr(string $col, string $locale = 'ca'): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "json_extract($col, '\$.\"$locale\"')"
            : "JSON_UNQUOTE(JSON_EXTRACT($col, '\$.\"$locale\"'))";
    }

    /*
    |--------------------------------------------------------------------------
    | Main Reports Dashboard
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // KPI Cards
        $kpis = [
            'total_revenue'     => Order::where('status', '!=', 'cancelled')->sum('total'),
            'total_orders'      => Order::count(),
            'total_clients'     => User::where('role', 'approved')->count(),
            'pending_approvals' => User::where('role', 'pending')->count(),
            'avg_order_value'   => Order::where('status', '!=', 'cancelled')->avg('total') ?? 0,
            'open_quotations'   => Quotation::whereIn('status', ['pending', 'reviewing', 'quoted'])->count(),
        ];

        // Monthly revenue — last 12 months
        $monthlyRevenue = Order::select(
            DB::raw($this->yearExpr('created_at') . ' as year'),
            DB::raw($this->monthExpr('created_at') . ' as month'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as order_count')
        )
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('created_at')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'label'       => \Carbon\Carbon::createFromDate($row->year, $row->month, 1)
                    ->translatedFormat('M Y'),
                'revenue'     => round((float)$row->revenue, 2),
                'order_count' => (int)$row->order_count,
            ]);

        // Top 5 products by revenue
        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(total) as revenue'),
            DB::raw('SUM(quantity) as units_sold')
        )
            ->whereHas('order', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->with('product:id,sku,brand')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        // Top 5 clients by spend
        $topClients = Order::select(
            'user_id',
            DB::raw('SUM(total) as total_spend'),
            DB::raw('COUNT(*) as order_count')
        )
            ->where('status', '!=', 'cancelled')
            ->with('user:id,name,company_name,email')
            ->groupBy('user_id')
            ->orderByDesc('total_spend')
            ->take(5)
            ->get();

        // Order status breakdown
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($row) => [$row->status => $row->count]);

        return view('admin.reports.index', compact(
            'kpis',
            'monthlyRevenue',
            'topProducts',
            'topClients',
            'ordersByStatus'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Revenue Report
    |--------------------------------------------------------------------------
    */

    public function revenue(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month');

        // Monthly breakdown for selected year
        $monthlyData = Order::select(
            DB::raw($this->monthExpr('created_at') . ' as month'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('SUM(subtotal) as subtotal'),
            DB::raw('SUM(vat_amount) as vat'),
            DB::raw('COUNT(*) as orders')
        )
            ->where('status', '!=', 'cancelled')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Fill all 12 months
        $months = collect(range(1, 12))->map(fn($m) => [
            'month'    => $m,
            'label'    => \Carbon\Carbon::createFromDate($year, $m, 1)->translatedFormat('F'),
            'revenue'  => round((float)($monthlyData[$m]->revenue ?? 0), 2),
            'subtotal' => round((float)($monthlyData[$m]->subtotal ?? 0), 2),
            'vat'      => round((float)($monthlyData[$m]->vat ?? 0), 2),
            'orders'   => (int)($monthlyData[$m]->orders ?? 0),
        ]);

        // Year-over-year comparison
        $yearlyTotals = Order::select(
            DB::raw($this->yearExpr('created_at') . ' as year'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as orders')
        )
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('created_at')
            ->groupBy('year')
            ->orderByDesc('year')
            ->take(4)
            ->get();

        // Available years for filter
        $availableYears = Order::selectRaw($this->yearExpr('created_at') . ' as year')
            ->whereNotNull('created_at')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year');

        return view('admin.reports.revenue', compact(
            'months',
            'yearlyTotals',
            'availableYears',
            'year'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Products Report
    |--------------------------------------------------------------------------
    */

    public function products(Request $request)
    {
        $sortBy = $request->input('sort', 'revenue');

        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(total) as revenue'),
            DB::raw('SUM(quantity) as units_sold'),
            DB::raw('COUNT(DISTINCT order_id) as times_ordered'),
            DB::raw('AVG(unit_price) as avg_price')
        )
            ->whereHas('order', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->with('product:id,sku,brand,stock,price,is_active')
            ->groupBy('product_id')
            ->orderByDesc($sortBy === 'units' ? 'units_sold' : 'revenue')
            ->paginate(20);

        // Category breakdown
        $categoryRevenue = OrderItem::select(
            DB::raw('SUM(order_items.total) as revenue'),
            DB::raw('SUM(order_items.quantity) as units_sold')
        )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->addSelect('categories.id as category_id')
            ->selectRaw($this->jsonNameExpr('categories.name') . ' as category_name')
            ->whereHas('order', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        return view('admin.reports.products', compact(
            'topProducts',
            'categoryRevenue',
            'sortBy'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Clients Report
    |--------------------------------------------------------------------------
    */

    public function clients(Request $request)
    {
        $sortBy = $request->input('sort', 'spend');

        $clients = User::where('role', 'approved')
            ->withCount('orders')
            ->withSum(
                ['orders as total_spend' => fn($q) => $q->where('status', '!=', 'cancelled')],
                'total'
            )
            ->withMax('orders as last_order_at', 'created_at')
            ->orderByDesc($sortBy === 'orders' ? 'orders_count' : 'total_spend')
            ->paginate(20);

        // Clients registered per month (last 12)
        $newClientsMonthly = User::select(
            DB::raw($this->yearExpr('created_at') . ' as year'),
            DB::raw($this->monthExpr('created_at') . ' as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('role', 'approved')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'label' => \Carbon\Carbon::createFromDate($row->year, $row->month, 1)
                    ->translatedFormat('M Y'),
                'count' => (int)$row->count,
            ]);

        // Pending clients
        $pendingCount  = User::where('role', 'pending')->count();
        $rejectedCount = User::where('role', 'rejected')->count();

        return view('admin.reports.clients', compact(
            'clients',
            'newClientsMonthly',
            'pendingCount',
            'rejectedCount',
            'sortBy'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Print Jobs Report
    |--------------------------------------------------------------------------
    */

    public function printJobs(Request $request)
    {
        $activeStatuses = ['ordered', 'in_production', 'completed'];

        // Summary KPIs
        $kpis = [
            'total_jobs'      => PrintJob::whereIn('status', $activeStatuses)->count(),
            'total_revenue'   => PrintJob::whereIn('status', $activeStatuses)->sum('total_price'),
            'jobs_ordered'    => PrintJob::where('status', 'ordered')->count(),
            'jobs_production' => PrintJob::where('status', 'in_production')->count(),
            'jobs_completed'  => PrintJob::where('status', 'completed')->count(),
            'jobs_cancelled'  => PrintJob::where('status', 'cancelled')->count(),
            'no_artwork'      => PrintJob::whereIn('status', ['ordered', 'in_production'])->whereNull('artwork_path')->count(),
            'avg_quantity'    => (int) round(PrintJob::whereIn('status', $activeStatuses)->avg('quantity') ?? 0),
        ];

        // Revenue + volume by month — last 6 months
        $monthlyData = collect(range(5, 0))->map(function ($monthsAgo) use ($activeStatuses) {
            $date = now()->subMonths($monthsAgo);
            $jobs = PrintJob::whereIn('status', $activeStatuses)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);
            return [
                'month'   => $date->translatedFormat('M'),
                'year'    => $date->year,
                'count'   => $jobs->count(),
                'revenue' => (float) $jobs->sum('total_price'),
            ];
        });

        // Revenue breakdown by template
        $byTemplate = PrintJob::whereIn('status', $activeStatuses)
            ->select('print_template_id', DB::raw('COUNT(*) as job_count'), DB::raw('SUM(total_price) as revenue'), DB::raw('SUM(quantity) as total_units'))
            ->with('template:id,name,slug')
            ->groupBy('print_template_id')
            ->orderByDesc('revenue')
            ->get();

        // Top clients by print volume
        $topClients = PrintJob::whereIn('status', $activeStatuses)
            ->select('user_id', DB::raw('COUNT(*) as job_count'), DB::raw('SUM(total_price) as revenue'), DB::raw('SUM(quantity) as total_units'))
            ->with('user:id,name,company_name')
            ->groupBy('user_id')
            ->orderByDesc('revenue')
            ->take(10)
            ->get();

        // Avg production time (days from ordered_at to produced_at for completed jobs)
        $avgProductionDays = PrintJob::where('status', 'completed')
            ->whereNotNull('produced_at')
            ->selectRaw('AVG(' . $this->dateDiffExpr('produced_at', 'created_at') . ') as avg_days')
            ->value('avg_days');

        return view('admin.reports.print_jobs', compact(
            'kpis',
            'monthlyData',
            'byTemplate',
            'topClients',
            'avgProductionDays'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Export Print Jobs CSV
    |--------------------------------------------------------------------------
    */

    public function exportPrintJobs(): StreamedResponse
    {
        $jobs = PrintJob::whereNotIn('status', ['draft', 'in_cart'])
            ->with(['user', 'template'])
            ->orderByDesc('created_at')
            ->get();

        return response()->streamDownload(function () use ($jobs) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                'Data',
                'Client',
                'Empresa',
                'Email',
                'Plantilla',
                'Quantitat',
                'Preu unitari (€)',
                'Total (€)',
                'Estat',
                'Arxiu disseny',
                'Entrega prevista',
            ]);

            foreach ($jobs as $job) {
                fputcsv($handle, [
                    $job->id,
                    $job->created_at?->format('d/m/Y'),
                    $job->user?->name,
                    $job->user?->company_name,
                    $job->user?->email,
                    $job->template?->getTranslation('name', 'ca') ?? '—',
                    $job->quantity,
                    number_format($job->unit_price, 4, '.', ''),
                    number_format($job->total_price, 2, '.', ''),
                    $job->status,
                    $job->artwork_path ? 'Sí' : 'No',
                    $job->expected_delivery_at?->format('d/m/Y') ?? '—',
                ]);
            }

            fclose($handle);
        }, 'print_jobs_' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Export Orders CSV
    |--------------------------------------------------------------------------
    */

    public function exportOrders(Request $request): StreamedResponse
    {
        $from = $request->input('from', now()->startOfYear()->toDateString());
        $to   = $request->input('to', now()->toDateString());

        $orders = Order::with(['user', 'items'])
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at')
            ->get();

        $filename = 'orders_' . $from . '_to_' . $to . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'Order #',
                'Date',
                'Client',
                'Company',
                'Email',
                'Items',
                'Subtotal (€)',
                'VAT (€)',
                'Total (€)',
                'Status',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at?->format('d/m/Y'),
                    $order->user?->name,
                    $order->user?->company_name,
                    $order->user?->email,
                    $order->items->sum('quantity'),
                    number_format($order->subtotal, 2, '.', ''),
                    number_format($order->vat_amount, 2, '.', ''),
                    number_format($order->total, 2, '.', ''),
                    $order->status,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Export Clients CSV
    |--------------------------------------------------------------------------
    */

    public function exportClients(): StreamedResponse
    {
        $clients = User::where('role', 'approved')
            ->withCount('orders')
            ->withSum(
                ['orders as total_spend' => fn($q) => $q->where('status', '!=', 'cancelled')],
                'total'
            )
            ->orderByDesc('total_spend')
            ->get();

        return response()->streamDownload(function () use ($clients) {
            $handle = fopen('php://output', 'w');

            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Name',
                'Company',
                'Email',
                'Phone',
                'CIF',
                'City',
                'Country',
                'Total Orders',
                'Total Spend (€)',
                'Registered At',
            ]);

            foreach ($clients as $client) {
                fputcsv($handle, [
                    $client->name,
                    $client->company_name,
                    $client->email,
                    $client->phone,
                    $client->cif,
                    $client->city,
                    $client->country,
                    $client->orders_count,
                    number_format($client->total_spend ?? 0, 2, '.', ''),
                    $client->created_at->format('d/m/Y'),
                ]);
            }

            fclose($handle);
        }, 'clients_' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
