<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title'  => ['nullable', 'string', 'max:120'],
            'body'   => ['required', 'string', 'min:10', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:4'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'order_id' => ['nullable', 'integer'],
        ]);

        // Prevent duplicate review
        $existing = ProductReview::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->with('error', __('app.review_already_submitted'));
        }

        // Verify order belongs to user if provided
        $orderId = null;
        if ($request->order_id) {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', Auth::id())
                ->where('status', 'delivered')
                ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                ->first();
            $orderId = $order?->id;
        }

        // Upload photos
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('reviews', 'public');
            }
        }

        ProductReview::create([
            'product_id'  => $product->id,
            'user_id'     => Auth::id(),
            'order_id'    => $orderId,
            'rating'      => $request->rating,
            'title'       => $request->title,
            'body'        => $request->body,
            'photos'      => $photoPaths ?: null,
            'is_approved' => false,
        ]);

        return back()->with('success', __('app.review_submitted_pending'));
    }

    public function destroy(int $id)
    {
        $review = ProductReview::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Delete photos from storage
        if ($review->photos) {
            foreach ($review->photos as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $review->delete();

        return back()->with('success', __('app.review_deleted'));
    }
}
