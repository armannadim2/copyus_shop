<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Notifications\QuotationPricedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminQuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::with('user')
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('quote_number', 'like', "%$s%")
                    ->orWhereHas(
                        'user',
                        fn($q) =>
                        $q->where('name', 'like', "%$s%")
                            ->orWhere('company_name', 'like', "%$s%")
                    )
            )
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.quotations.index', compact('quotations'));
    }

    public function show(string $quoteNumber)
    {
        $quotation = Quotation::where('quote_number', $quoteNumber)
            ->with('user', 'items.product')
            ->firstOrFail();

        return view('admin.quotations.show', compact('quotation'));
    }

    public function setPrice(Request $request, string $quoteNumber)
    {
        $quotation = Quotation::where('quote_number', $quoteNumber)
            ->with('items')
            ->firstOrFail();

        $request->validate([
            'items'             => ['required', 'array'],
            'items.*.id'        => ['required', 'exists:quotation_items,id'],
            'items.*.price'     => ['required', 'numeric', 'min:0'],
            'admin_notes'       => ['nullable', 'string', 'max:1000'],
            'valid_until'       => ['required', 'date', 'after:today'],
        ]);

        DB::transaction(function () use ($request, $quotation) {

            $total = 0;

            foreach ($request->items as $itemData) {
                $item = QuotationItem::findOrFail($itemData['id']);
                $item->update(['quoted_price' => $itemData['price']]);
                $total += $itemData['price'] * $item->quantity;
            }

            $quotation->update([
                'status'       => 'quoted',
                'quoted_total' => $total,
                'admin_notes'  => $request->admin_notes,
                'valid_until'  => $request->valid_until,
            ]);
        });

        // Notify customer their quote has been priced
        $quotation->refresh();
        $quotation->user->notify(new QuotationPricedNotification($quotation));

        return back()->with('success', "Pressupost enviat al client. ✅");
    }

    public function updateStatus(Request $request, string $quoteNumber)
    {
        $request->validate([
            'status' => ['required', 'in:pending,reviewing,quoted,accepted,rejected,expired,converted'],
        ]);

        $quotation = Quotation::where('quote_number', $quoteNumber)->firstOrFail();
        $quotation->update(['status' => $request->status]);

        return back()->with('success', "Estat del pressupost actualitzat. ✅");
    }
}
