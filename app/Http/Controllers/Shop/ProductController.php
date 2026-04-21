<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Product listing — public, but prices only shown to approved B2B users
     */
    public function index(Request $request)
    {
        $isB2B = Auth::check() && in_array(Auth::user()->role, ['approved', 'admin']);

        // ── Category-scoped base query (for deriving available filter options) ──
        $categoryScope = fn($q) => $q->whereHas('category', fn($q) => $q
            ->where('slug', $request->category)
            ->orWhereHas('parent', fn($q) => $q->where('slug', $request->category))
        );

        $contextQuery = Product::active()
            ->when($request->category, $categoryScope);

        // Brands available in this category context, with per-brand product counts
        $availableBrands = (clone $contextQuery)
            ->whereNotNull('brand')->where('brand', '!=', '')
            ->selectRaw('brand, COUNT(*) as cnt')
            ->groupBy('brand')
            ->orderBy('brand')
            ->pluck('cnt', 'brand');

        // Only show seasonal filter if this category actually has seasonal products
        $hasSeasonalProducts = (clone $contextQuery)->where('is_seasonal', true)->exists();

        // Context price range — used as input placeholders
        $priceRange = (clone $contextQuery)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $products = Product::with('category')
            ->active()
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('sku', 'like', "%$s%")
                    ->orWhere('brand', 'like', "%$s%")
            )
            ->when(
                $request->category,
                fn($q, $c) => $q->whereHas('category', fn($q) => $q
                    ->where('slug', $c)
                    ->orWhereHas('parent', fn($q) => $q->where('slug', $c))
                )
            )
            ->when($request->in_stock,   fn($q) => $q->where('stock', '>', 0))
            ->when($request->price_min,  fn($q, $min) => $q->where('price', '>=', $min))
            ->when($request->price_max,  fn($q, $max) => $q->where('price', '<=', $max))
            ->when($request->brand,      fn($q, $brands) => $q->whereIn('brand', (array) $brands))
            ->when($request->seasonal,   fn($q) => $q->where('is_seasonal', true))
            ->paginate(24)
            ->withQueryString();

        // Parent categories with children, counting only active products
        $categories = Category::active()
            ->parents()
            ->with(['children' => fn($q) => $q->active()
                ->withCount(['products as products_count' => fn($q) => $q->active()])
                ->ordered()
            ])
            ->withCount(['products as own_count' => fn($q) => $q->active()])
            ->ordered()
            ->get()
            ->map(function ($cat) {
                // Filter out children with no products
                $cat->children = $cat->children->filter(fn($c) => $c->products_count > 0)->values();
                // Total = own products + sum of children's products
                $cat->products_count = $cat->own_count + $cat->children->sum('products_count');
                return $cat;
            })
            ->filter(fn($cat) => $cat->products_count > 0)
            ->values();

        $wishlistIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->flip()
            : collect();

        return view('shop.products.index', compact(
            'products',
            'categories',
            'isB2B',
            'wishlistIds',
            'availableBrands',
            'hasSeasonalProducts',
            'priceRange'
        ));
    }

    /**
     * Single product — public, prices gated
     */
    public function show(string $slug)
    {
        $isB2B = Auth::check() && in_array(Auth::user()->role, ['approved', 'admin']);
        $user  = Auth::user();

        $product = Product::with(['category', 'images', 'variants' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'), 'tags'])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        // Resolve active price tiers for display (quantity-based, no user filter)
        $priceTiers = $product->priceTiers()
            ->active()
            ->whereNull('user_id')
            ->orderBy('min_quantity')
            ->get();

        // User-specific tier (if logged in B2B)
        $userTier = $user
            ? $product->priceTiers()->active()->where('user_id', $user->id)->first()
            : null;

        $related = Product::with('category')
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        // "Customers also ordered" — products co-purchased in same orders
        $alsoOrderedIds = OrderItem::whereIn(
                'order_id',
                OrderItem::where('product_id', $product->id)->select('order_id')
            )
            ->where('product_id', '!=', $product->id)
            ->selectRaw('product_id, COUNT(*) as co_count')
            ->groupBy('product_id')
            ->orderByDesc('co_count')
            ->limit(4)
            ->pluck('product_id');

        $alsoOrdered = $alsoOrderedIds->isNotEmpty()
            ? Product::active()->whereIn('id', $alsoOrderedIds)->get()->sortBy(
                fn($p) => $alsoOrderedIds->search($p->id)
            )->values()
            : collect();

        $inWishlist = $user
            ? Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->exists()
            : false;

        return view('shop.products.show', compact(
            'product',
            'related',
            'alsoOrdered',
            'isB2B',
            'priceTiers',
            'userTier',
            'inWishlist'
        ));
    }

    /**
     * Products by category — public
     */
    public function category(string $slug)
    {
        $isB2B = Auth::check() && in_array(Auth::user()->role, ['approved', 'admin']);

        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        // Include products from subcategories when viewing a parent category page
        $categoryIds = collect([$category->id]);
        if (is_null($category->parent_id)) {
            $childIds = Category::active()->where('parent_id', $category->id)->pluck('id');
            $categoryIds = $categoryIds->merge($childIds);
        }

        $contextQuery = Product::active()->whereIn('category_id', $categoryIds);

        $availableBrands = (clone $contextQuery)
            ->whereNotNull('brand')->where('brand', '!=', '')
            ->selectRaw('brand, COUNT(*) as cnt')
            ->groupBy('brand')
            ->orderBy('brand')
            ->pluck('cnt', 'brand');

        $hasSeasonalProducts = (clone $contextQuery)->where('is_seasonal', true)->exists();

        $priceRange = (clone $contextQuery)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $products = Product::with('category')
            ->active()
            ->whereIn('category_id', $categoryIds)
            ->when(request('in_stock'),  fn($q) => $q->where('stock', '>', 0))
            ->when(request('price_min'), fn($q, $min) => $q->where('price', '>=', $min))
            ->when(request('price_max'), fn($q, $max) => $q->where('price', '<=', $max))
            ->when(request('brand'),     fn($q, $brands) => $q->whereIn('brand', (array) $brands))
            ->when(request('seasonal'),  fn($q) => $q->where('is_seasonal', true))
            ->paginate(24);

        $categories = Category::active()
            ->parents()
            ->with(['children' => fn($q) => $q->active()
                ->withCount(['products as products_count' => fn($q) => $q->active()])
                ->ordered()
            ])
            ->withCount(['products as own_count' => fn($q) => $q->active()])
            ->ordered()
            ->get()
            ->map(function ($cat) {
                $cat->children = $cat->children->filter(fn($c) => $c->products_count > 0)->values();
                $cat->products_count = $cat->own_count + $cat->children->sum('products_count');
                return $cat;
            })
            ->filter(fn($cat) => $cat->products_count > 0)
            ->values();

        $wishlistIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->flip()
            : collect();

        return view('shop.products.index', compact(
            'products',
            'category',
            'categories',
            'isB2B',
            'wishlistIds',
            'availableBrands',
            'hasSeasonalProducts',
            'priceRange'
        ));
    }
}
