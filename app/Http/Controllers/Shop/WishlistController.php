<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())
            ->with('product.category', 'product.images')
            ->latest()
            ->get();

        return view('shop.wishlist.index', compact('items'));
    }

    public function toggle(int $productId)
    {
        $product = Product::findOrFail($productId);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $productId]);
            $inWishlist = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['in_wishlist' => $inWishlist]);
        }

        return back()->with(
            'success',
            $inWishlist ? __('app.added_to_wishlist') : __('app.removed_from_wishlist')
        );
    }

    public function destroy(int $productId)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();

        return back()->with('success', __('app.removed_from_wishlist'));
    }
}
