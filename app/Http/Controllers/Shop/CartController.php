<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\PrintJob;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->where('type', 'cart')
            ->with(['product', 'printJob.template'])
            ->get();

        $subtotal  = $cartItems->sum(fn($i) => $i->effective_unit_price * $i->quantity);
        $vatAmount = $cartItems->sum(fn($i) => $i->effective_unit_price * ($i->effective_vat_rate / 100) * $i->quantity);
        $total     = $cartItems->sum(fn($i) => $i->line_total);

        return view('shop.cart.index', compact('cartItems', 'subtotal', 'vatAmount', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity < $product->min_order_quantity) {
            return back()->with('error',
                'La quantitat mínima per a aquest producte és ' . $product->min_order_quantity
            );
        }

        CartItem::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id,
                'type'       => 'cart',
            ],
            [
                'quantity' => \DB::raw('quantity + ' . (int) $request->quantity),
            ]
        );

        return back()->with('success', __('app.add_to_cart') . ' ✅');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item = CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('type', 'cart')
            ->firstOrFail();

        $item->update(['quantity' => $request->quantity]);

        // Keep PrintJob quantity in sync
        if ($item->print_job_id) {
            PrintJob::where('id', $item->print_job_id)->update(['quantity' => $request->quantity]);
        }

        return back()->with('success', __('app.save') . ' ✅');
    }

    public function remove(int $id)
    {
        $item = CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('type', 'cart')
            ->firstOrFail();

        // Delete the associated print job (draft only)
        if ($item->print_job_id) {
            PrintJob::where('id', $item->print_job_id)
                ->where('status', 'in_cart')
                ->delete();
        }

        $item->delete();

        return back()->with('success', __('app.remove_item') . ' ✅');
    }

    public function clear()
    {
        $items = CartItem::where('user_id', Auth::id())
            ->where('type', 'cart')
            ->with('printJob')
            ->get();

        // Delete any print jobs in_cart status
        $printJobIds = $items->whereNotNull('print_job_id')->pluck('print_job_id');
        if ($printJobIds->isNotEmpty()) {
            PrintJob::whereIn('id', $printJobIds)->where('status', 'in_cart')->delete();
        }

        CartItem::where('user_id', Auth::id())
            ->where('type', 'cart')
            ->delete();

        return back()->with('success', __('app.clear_cart') . ' ✅');
    }
}
