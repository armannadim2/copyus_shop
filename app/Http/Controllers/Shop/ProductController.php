<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Brand;
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
        $availableBrands = Brand::active()
            ->whereHas('products', fn($q) => $q->active()->when($request->category, $categoryScope))
            ->withCount(['products as cnt' => fn($q) => $q->active()->when($request->category, $categoryScope)])
            ->ordered()
            ->get();

        // Only show seasonal filter if this category actually has seasonal products
        $hasSeasonalProducts = (clone $contextQuery)->where('is_seasonal', true)->exists();

        // Context price range — used as input placeholders
        $priceRange = (clone $contextQuery)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $sort = $request->get('sort', 'newest');

        $products = Product::with('category')
            ->active()
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('sku', 'like', "%$s%")
                    ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', "%$s%"))
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
            ->when($request->brand,      fn($q, $brands) => $q->whereIn('brand_id', (array) $brands))
            ->when($request->seasonal,   fn($q) => $q->where('is_seasonal', true))
            ->when($sort === 'price_asc',  fn($q) => $q->orderBy('price', 'asc'))
            ->when($sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
            ->when($sort === 'name_asc',   fn($q) => $q->orderBy('name', 'asc'))
            ->when($sort === 'name_desc',  fn($q) => $q->orderBy('name', 'desc'))
            ->when(!in_array($sort, ['price_asc','price_desc','name_asc','name_desc']), fn($q) => $q->orderByDesc('created_at'))
            ->paginate(24)
            ->withQueryString();

        $activeSlug = request('category');
        [$categories, $openIds] = $this->buildCategoryTree($activeSlug);

        $wishlistIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->flip()
            : collect();

        return view('shop.products.index', compact(
            'products',
            'categories',
            'openIds',
            'activeSlug',
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

        $product = Product::with(['category.parent.parent', 'images', 'variants' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'), 'tags'])
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

        $availableBrands = Brand::active()
            ->whereHas('products', fn($q) => $q->active()->whereIn('category_id', $categoryIds))
            ->withCount(['products as cnt' => fn($q) => $q->active()->whereIn('category_id', $categoryIds)])
            ->ordered()
            ->get();

        $hasSeasonalProducts = (clone $contextQuery)->where('is_seasonal', true)->exists();

        $priceRange = (clone $contextQuery)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $sort = request('sort', 'newest');

        $products = Product::with('category')
            ->active()
            ->whereIn('category_id', $categoryIds)
            ->when(request('in_stock'),  fn($q) => $q->where('stock', '>', 0))
            ->when(request('price_min'), fn($q, $min) => $q->where('price', '>=', $min))
            ->when(request('price_max'), fn($q, $max) => $q->where('price', '<=', $max))
            ->when(request('brand'),     fn($q, $brands) => $q->whereIn('brand_id', (array) $brands))
            ->when(request('seasonal'),  fn($q) => $q->where('is_seasonal', true))
            ->when($sort === 'price_asc',  fn($q) => $q->orderBy('price', 'asc'))
            ->when($sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
            ->when($sort === 'name_asc',   fn($q) => $q->orderBy('name', 'asc'))
            ->when($sort === 'name_desc',  fn($q) => $q->orderBy('name', 'desc'))
            ->when(!in_array($sort, ['price_asc','price_desc','name_asc','name_desc']), fn($q) => $q->orderByDesc('created_at'))
            ->paginate(24)
            ->withQueryString();

        $activeSlug = $slug;
        [$categories, $openIds] = $this->buildCategoryTree($activeSlug);

        $wishlistIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->flip()
            : collect();

        return view('shop.products.index', compact(
            'products',
            'category',
            'categories',
            'openIds',
            'activeSlug',
            'isB2B',
            'wishlistIds',
            'availableBrands',
            'hasSeasonalProducts',
            'priceRange'
        ));
    }

    private function buildCategoryTree(?string $activeSlug): array
    {
        // One query — all active categories with their own product count
        $allCats = Category::active()
            ->withCount(['products as products_count' => fn($q) => $q->active()])
            ->ordered()
            ->get();

        // Group by parent_id into a plain array (null parent → 'root')
        $byParent = [];
        foreach ($allCats as $cat) {
            $byParent[$cat->parent_id ?? 'root'][] = $cat;
        }

        // Recursively build tree; each node gets subtree + total_count
        $buildTree = function ($parentId) use (&$buildTree, &$byParent) {
            $key = $parentId ?? 'root';
            return collect($byParent[$key] ?? [])
                ->map(function ($cat) use (&$buildTree) {
                    $cat->subtree     = $buildTree($cat->id);
                    $cat->total_count = $cat->products_count + $cat->subtree->sum('total_count');
                    return $cat;
                })
                ->filter(fn($cat) => $cat->total_count > 0)
                ->values();
        };

        $categories = $buildTree(null);

        // Walk up ancestors of the active category to know which nodes to auto-open
        $openIds = [];
        if ($activeSlug) {
            $activeCat = $allCats->firstWhere('slug', $activeSlug);
            if ($activeCat) {
                $cur = $activeCat;
                while ($cur->parent_id) {
                    $openIds[] = $cur->parent_id;
                    $cur = $allCats->find($cur->parent_id);
                    if (!$cur) break;
                }
            }
        }

        return [$categories, $openIds];
    }
}
