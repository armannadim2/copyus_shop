<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\PrintJob;
use App\Models\PrintTemplate;
use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Public homepage
     */
    public function index()
    {
        $isB2B = Auth::check() && in_array(Auth::user()->role, ['approved', 'admin']);

        $featuredProducts = Product::with('category')
            ->active()
            ->featured()
            ->take(8)
            ->get();

        $categories = Category::active()
            ->ordered()
            ->withCount(['activeProducts'])
            ->get();

        $printTemplates = PrintTemplate::active()
            ->with(['quantityTiers' => fn($q) => $q->orderBy('min_quantity')])
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('shop.home', compact(
            'featuredProducts',
            'categories',
            'isB2B',
            'printTemplates'
        ));
    }

    /**
     * B2B Dashboard — auth + approved only
     */
    public function dashboard()
    {
        $user = Auth::user();

        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentQuotes = Quotation::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $totalOrders   = Order::where('user_id', $user->id)->count();
        $totalQuotes   = Quotation::where('user_id', $user->id)->count();

        // Quotes requiring action (quoted & not expired)
        $quotesAwaiting = Quotation::where('user_id', $user->id)
            ->where('status', 'quoted')
            ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
            ->count();

        // Active orders (in progress)
        $activeOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'processing', 'shipped'])
            ->count();

        // Total spent (all time)
        $totalSpent = Order::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total');

        // Spent this year
        $spentThisYear = Order::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Last order date
        $lastOrder = Order::where('user_id', $user->id)->latest()->first();

        // Print jobs
        $recentPrintJobs = PrintJob::where('user_id', $user->id)
            ->whereNotIn('status', ['draft', 'in_cart'])
            ->with('template')
            ->latest()
            ->take(5)
            ->get();

        $activePrintJobs = PrintJob::where('user_id', $user->id)
            ->whereIn('status', ['ordered', 'in_production'])
            ->count();

        $pendingArtwork = PrintJob::where('user_id', $user->id)
            ->whereIn('status', ['ordered', 'in_production'])
            ->whereNull('artwork_path')
            ->count();

        return view('shop.dashboard', compact(
            'user',
            'recentOrders',
            'recentQuotes',
            'totalOrders',
            'totalQuotes',
            'quotesAwaiting',
            'activeOrders',
            'totalSpent',
            'spentThisYear',
            'lastOrder',
            'recentPrintJobs',
            'activePrintJobs',
            'pendingArtwork'
        ));
    }
}
