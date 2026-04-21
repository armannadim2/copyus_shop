<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query      = trim($request->input('q', ''));
        $locale     = app()->getLocale();

        if (strlen($query) < 2) {
            return redirect()->route('products.index');
        }

        $products = Product::with('category')
            ->active()
            ->where(function ($q) use ($query) {
                $q->where('sku', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->when($request->category, fn($q, $c) =>
                $q->whereHas('category', fn($q) => $q->where('slug', $c))
            )
            ->when($request->in_stock, fn($q) => $q->where('stock', '>', 0))
            ->when($request->price_min, fn($q, $min) => $q->where('price', '>=', $min))
            ->when($request->price_max, fn($q, $max) => $q->where('price', '<=', $max))
            ->paginate(24)
            ->withQueryString();

        $categories  = Category::active()->ordered()->get();
        $wishlistIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->flip()
            : collect();

        return view('shop.search.results', compact('products', 'query', 'categories', 'wishlistIds'));
    }

    /**
     * AJAX autocomplete — returns up to 8 product suggestions as JSON.
     */
    public function autocomplete(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $locale   = app()->getLocale();
        $results  = Product::active()
            ->select('id', 'slug', 'name', 'sku', 'brand', 'image')
            ->where(function ($query) use ($q) {
                $query->where('sku',   'like', "%{$q}%")
                      ->orWhere('brand', 'like', "%{$q}%")
                      ->orWhere('name',  'like', "%{$q}%");
            })
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'label' => $p->getTranslation('name', $locale),
                'sku'   => $p->sku,
                'brand' => $p->brand,
                'url'   => route('products.show', $p->slug),
                'image' => $p->image ? asset('storage/' . $p->image) : null,
            ]);

        return response()->json($results);
    }
}
