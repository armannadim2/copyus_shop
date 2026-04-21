<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\QuotationSubmittedNotification;
use App\Notifications\Admin\NewOrderPlacedNotification;
use App\Notifications\Admin\NewQuotationSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('shop.quotations.index', compact('quotations'));
    }

    public function basket()
    {
        $quoteItems = CartItem::where('user_id', Auth::id())
            ->where('type', 'quote')
            ->with('product')
            ->get();

        return view('shop.quotations.basket', compact('quoteItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        CartItem::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id,
                'type'       => 'quote',
            ],
            [
                'quantity' => DB::raw('quantity + ' . (int) $request->quantity),
            ]
        );

        return back()->with('success', __('app.add_to_quote') . ' ✅');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('type', 'quote')
            ->update(['quantity' => $request->quantity]);

        return back()->with('success', __('app.save') . ' ✅');
    }

    public function remove(int $id)
    {
        CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('type', 'quote')
            ->delete();

        return back()->with('success', __('app.remove_item') . ' ✅');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user       = Auth::user();
        $quoteItems = CartItem::where('user_id', $user->id)
            ->where('type', 'quote')
            ->with('product')
            ->get();

        if ($quoteItems->isEmpty()) {
            return redirect()->route('quotations.basket')
                ->with('error', __('app.quote_basket_empty'));
        }

        $submittedQuotation = null;

        DB::transaction(function () use ($request, $user, $quoteItems, &$submittedQuotation) {

            $quotation = Quotation::create([
                'quote_number'   => Quotation::generateQuoteNumber(),
                'user_id'        => $user->id,
                'status'         => 'pending',
                'customer_notes' => $request->customer_notes,
                'locale'         => app()->getLocale(),
            ]);

            foreach ($quoteItems as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id'   => $item->product_id,
                    'quantity'     => $item->quantity,
                    'quoted_price' => null, // Admin fills later
                    'vat_rate'     => $item->product->vat_rate,
                ]);
            }

            $submittedQuotation = $quotation;

            // Clear quote basket
            CartItem::where('user_id', $user->id)
                ->where('type', 'quote')
                ->delete();
        });

        // Send confirmation email to customer + notify admins
        if ($submittedQuotation) {
            $submittedQuotation->load('items', 'user');
            $user->notify(new QuotationSubmittedNotification($submittedQuotation));
            User::admins()->get()
                ->each(fn($admin) => $admin->notify(new NewQuotationSubmittedNotification($submittedQuotation)));
        }

        return redirect()->route('quotations.index')
            ->with('success', __('app.quotation_submitted'));
    }

    public function show(string $quoteNumber)
    {
        $quotation = Quotation::where('quote_number', $quoteNumber)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        return view('shop.quotations.show', compact('quotation'));
    }

    public function accept(string $quoteNumber)
    {
        $quotation = Quotation::where('quote_number', $quoteNumber)
            ->where('user_id', Auth::id())
            ->where('status', 'quoted')
            ->with('items.product')
            ->firstOrFail();

        $user         = Auth::user();
        $createdOrder = null;
        $createdInvoice = null;

        DB::transaction(function () use ($quotation, $user, &$createdOrder, &$createdInvoice) {

            $billingAddress = [
                'company_name' => $user->company_name,
                'cif'          => $user->cif,
                'address'      => $user->address,
                'city'         => $user->city,
                'postal_code'  => $user->postal_code,
                'country'      => $user->country,
            ];

            $shippingAddress = $billingAddress;

            // Calculate totals from quoted prices
            $subtotal  = 0;
            $vatAmount = 0;

            foreach ($quotation->items as $item) {
                $vatRate      = (float) ($item->vat_rate ?? $item->product?->vat_rate ?? 21);
                $priceWithVat = (float) ($item->quoted_price ?? 0);
                $priceExVat   = round($priceWithVat / (1 + $vatRate / 100), 4);
                $lineVat      = round(($priceWithVat - $priceExVat) * $item->quantity, 4);
                $lineSubtotal = round($priceExVat * $item->quantity, 4);

                $subtotal  += $lineSubtotal;
                $vatAmount += $lineVat;
            }

            $total = round($subtotal + $vatAmount, 2);

            // Create Order
            $createdOrder = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'user_id'          => $user->id,
                'subtotal'         => $subtotal,
                'vat_amount'       => $vatAmount,
                'total'            => $total,
                'shipping_cost'    => 0,
                'status'           => 'confirmed',
                'payment_status'   => 'unpaid',
                'shipping_address' => $shippingAddress,
                'billing_address'  => $billingAddress,
                'notes'            => $quotation->customer_notes,
                'locale'           => $quotation->locale ?? app()->getLocale(),
            ]);

            // Create Order Items from quoted prices + decrement stock
            foreach ($quotation->items as $item) {
                $vatRate      = (float) ($item->vat_rate ?? $item->product?->vat_rate ?? 21);
                $priceWithVat = (float) ($item->quoted_price ?? 0);
                $priceExVat   = round($priceWithVat / (1 + $vatRate / 100), 4);
                $lineVat      = round(($priceWithVat - $priceExVat) * $item->quantity, 4);
                $lineSubtotal = round($priceExVat * $item->quantity, 4);
                $lineTotal    = round($priceWithVat * $item->quantity, 4);

                OrderItem::create([
                    'order_id'         => $createdOrder->id,
                    'product_id'       => $item->product_id,
                    'product_snapshot' => [
                        'name'  => $item->product?->getTranslation('name', app()->getLocale()) ?? '—',
                        'sku'   => $item->product?->sku ?? '—',
                        'brand' => $item->product?->brand ?? '',
                        'unit'  => $item->product?->unit ?? '',
                    ],
                    'quantity'   => $item->quantity,
                    'unit_price' => $priceExVat,
                    'vat_rate'   => $vatRate,
                    'vat_amount' => $lineVat,
                    'subtotal'   => $lineSubtotal,
                    'total'      => $lineTotal,
                ]);

                // Decrement stock
                if ($item->product_id) {
                    Product::where('id', $item->product_id)
                        ->update(['stock' => DB::raw('GREATEST(0, stock - ' . (int) $item->quantity . ')')]);
                }
            }

            // Create Invoice
            $createdInvoice = Invoice::create([
                'invoice_number'  => Invoice::generateInvoiceNumber(),
                'order_id'        => $createdOrder->id,
                'user_id'         => $user->id,
                'subtotal'        => $subtotal,
                'vat_amount'      => $vatAmount,
                'total'           => $total,
                'billing_address' => $billingAddress,
                'company_details' => [
                    'name'    => config('app.name'),
                    'address' => 'Carrer de Copyus, 1, Barcelona',
                    'cif'     => 'A00000000',
                    'email'   => 'info@copyus.es',
                    'phone'   => '+34 900 000 000',
                ],
                'issued_at' => now()->toDateString(),
                'due_at'    => now()->addDays(30)->toDateString(),
                'status'    => 'issued',
                'locale'    => $quotation->locale ?? app()->getLocale(),
            ]);

            // Mark quotation as converted
            $quotation->update([
                'status'              => 'converted',
                'converted_to_order_id' => $createdOrder->id,
            ]);
        });

        // Send confirmation email + admin notification
        if ($createdOrder && $createdInvoice) {
            $user->notify(new OrderConfirmedNotification($createdOrder, $createdInvoice));
            $createdOrder->load('user');
            User::admins()->get()
                ->each(fn($admin) => $admin->notify(new NewOrderPlacedNotification($createdOrder)));
        }

        return redirect()->route('orders.show', $createdOrder->order_number)
            ->with('success', __('app.quote_converted_success'));
    }
}
