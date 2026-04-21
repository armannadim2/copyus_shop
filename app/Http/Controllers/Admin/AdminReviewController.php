<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = ProductReview::with('product', 'user', 'order')
            ->when($request->status === 'pending',  fn($q) => $q->pending())
            ->when($request->status === 'approved', fn($q) => $q->approved())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(int $id)
    {
        $review = ProductReview::findOrFail($id);
        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Ressenya aprovada. ✅');
    }

    public function reject(int $id)
    {
        $review = ProductReview::findOrFail($id);
        $review->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Ressenya rebutjada.');
    }

    public function destroy(int $id)
    {
        $review = ProductReview::findOrFail($id);

        if ($review->photos) {
            foreach ($review->photos as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $review->delete();

        return back()->with('success', 'Ressenya eliminada.');
    }
}
