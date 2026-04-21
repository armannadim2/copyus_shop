<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintJob;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\SavedAddress;
use App\Models\User;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\Admin\NewOrderPlacedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }

    public function checkout()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->where('type', 'cart')
            ->with(['product', 'printJob.template'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('app.cart_empty'));
        }

        $subtotal  = $cartItems->sum(fn($i) => $i->effective_unit_price * $i->quantity);
        $vatAmount = $cartItems->sum(fn($i) => $i->effective_unit_price * ($i->effective_vat_rate / 100) * $i->quantity);
        $total     = $cartItems->sum(fn($i) => $i->line_total);
        $user      = Auth::user();

        $savedAddresses = SavedAddress::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderBy('label')
            ->get();

        return view('shop.orders.checkout',
            compact('cartItems', 'subtotal', 'vatAmount', 'total', 'user', 'savedAddresses')
        );
    }

    public function place(Request $request)
    {
        $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city'    => ['required', 'string', 'max:100'],
            'shipping_postal'  => ['required', 'string', 'max:10'],
            'shipping_country' => ['required', 'string', 'size:2'],
            'shipping_contact' => ['nullable', 'string', 'max:150'],
            'shipping_phone'   => ['nullable', 'string', 'max:30'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $user      = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)
            ->where('type', 'cart')
            ->with(['product', 'printJob.template'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('app.cart_empty'));
        }

        $placedOrder   = null;
        $placedInvoice = null;

        DB::transaction(function () use ($request, $user, $cartItems, &$placedOrder, &$placedInvoice) {

            $subtotal  = $cartItems->sum(fn($i) => $i->effective_unit_price * $i->quantity);
            $vatAmount = $cartItems->sum(fn($i) => $i->effective_unit_price * ($i->effective_vat_rate / 100) * $i->quantity);
            $total     = $cartItems->sum(fn($i) => $i->line_total);

            // Promo code discount
            $promoCode      = null;
            $discountAmount = 0;
            $promoCodeStr   = session('promo_code');
            if ($promoCodeStr) {
                $promoCode = PromoCode::where('code', $promoCodeStr)->where('is_active', true)->first();
                if ($promoCode && $promoCode->isValid($subtotal, $user) === true) {
                    $discountAmount = $promoCode->calculateDiscount($subtotal);
                    $total          = max(0, $total - $discountAmount);
                }
            }

            $shippingAddress = [
                'address'      => $request->shipping_address,
                'city'         => $request->shipping_city,
                'postal_code'  => $request->shipping_postal,
                'country'      => $request->shipping_country,
                'contact_name' => $request->shipping_contact,
                'phone'        => $request->shipping_phone,
            ];

            $billingAddress = [
                'company_name' => $user->company_name,
                'cif'          => $user->cif,
                'address'      => $user->address,
                'city'         => $user->city,
                'postal_code'  => $user->postal_code,
                'country'      => $user->country,
            ];

            // Resolve payment terms from company (if any)
            $company      = $user->company;
            $paymentTerms = $company?->payment_terms ?? 'immediate';
            $paymentDays  = $company?->payment_days ?? 0;
            $paymentDueAt = $paymentDays > 0 ? now()->addDays($paymentDays)->toDateString() : null;

            // Create Order
            $placedOrder = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'user_id'          => $user->id,
                'subtotal'         => $subtotal,
                'vat_amount'       => $vatAmount,
                'total'            => $total,
                'shipping_cost'    => 0,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'payment_terms'    => $paymentTerms,
                'payment_due_at'   => $paymentDueAt,
                'shipping_address' => $shippingAddress,
                'billing_address'  => $billingAddress,
                'notes'           => $request->notes,
                'locale'          => app()->getLocale(),
                'promo_code'      => $promoCode?->code,
                'discount_amount' => $discountAmount,
            ]);

            // Create Order Items
            foreach ($cartItems as $item) {
                $unitPrice    = $item->effective_unit_price;
                $vatRate      = $item->effective_vat_rate;
                $itemVatAmount = $unitPrice * ($vatRate / 100) * $item->quantity;
                $subtotalLine = $unitPrice * $item->quantity;
                $totalLine    = $item->line_total;

                if ($item->is_print_job) {
                    // Print job item
                    $job = $item->printJob;
                    OrderItem::create([
                        'order_id'         => $placedOrder->id,
                        'product_id'       => null,
                        'product_snapshot' => $job ? $job->toOrderSnapshot() : ['type' => 'print_job'],
                        'quantity'         => $item->quantity,
                        'unit_price'       => $unitPrice,
                        'vat_rate'         => $vatRate,
                        'vat_amount'       => $itemVatAmount,
                        'subtotal'         => $subtotalLine,
                        'total'            => $totalLine,
                    ]);

                    // Transition print job to ordered status
                    if ($job) {
                        $job->update(['status' => 'ordered']);
                    }
                } else {
                    // Regular product item
                    OrderItem::create([
                        'order_id'         => $placedOrder->id,
                        'product_id'       => $item->product_id,
                        'product_snapshot' => [
                            'name'  => $item->product->getTranslation('name', app()->getLocale()),
                            'sku'   => $item->product->sku,
                            'brand' => $item->product->brand,
                            'unit'  => $item->product->unit,
                        ],
                        'quantity'   => $item->quantity,
                        'unit_price' => $unitPrice,
                        'vat_rate'   => $vatRate,
                        'vat_amount' => $itemVatAmount,
                        'subtotal'   => $subtotalLine,
                        'total'      => $totalLine,
                    ]);

                    // Decrement stock (floor at 0)
                    Product::where('id', $item->product_id)
                        ->update([
                            'stock' => DB::raw('GREATEST(0, stock - ' . (int) $item->quantity . ')'),
                        ]);
                }
            }

            // Create Invoice automatically
            $placedInvoice = Invoice::create([
                'invoice_number'  => Invoice::generateInvoiceNumber(),
                'order_id'        => $placedOrder->id,
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
                'locale'    => app()->getLocale(),
            ]);

            // Increment promo code usage and clear session
            if ($promoCode) {
                $promoCode->increment('used_count');
            }

            // Clear Cart
            CartItem::where('user_id', $user->id)
                ->where('type', 'cart')
                ->delete();
        });

        session()->forget('promo_code');

        // Send confirmation email with PDF invoice
        if ($placedOrder && $placedInvoice) {
            $user->notify(new OrderConfirmedNotification($placedOrder, $placedInvoice));
        }

        // Notify admins of new order
        if ($placedOrder) {
            $placedOrder->load('user');
            User::admins()->get()
                ->each(fn($admin) => $admin->notify(new NewOrderPlacedNotification($placedOrder)));
        }

        // Fire low-stock notifications for affected products (skip print jobs)
        $productIds = $cartItems->whereNull('print_job_id')->pluck('product_id')->filter();
        Product::whereIn('id', $productIds)
            ->where('notify_low_stock', true)
            ->whereNotNull('low_stock_threshold')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->get()
            ->each(function ($product) {
                \App\Models\User::admins()->get()
                    ->each(fn($admin) => $admin->notify(new LowStockNotification($product)));
            });

        return redirect()->route('orders.show', $placedOrder->order_number)
            ->with('success', __('app.order_placed_success'));
    }

    public function submitPaymentReference(Request $request, string $orderNumber)
    {
        $request->validate([
            'payment_reference' => ['required', 'string', 'max:100'],
        ]);

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('payment_status', 'unpaid')
            ->firstOrFail();

        $order->update(['payment_reference' => $request->payment_reference]);

        return back()->with('success', __('app.payment_reference_submitted'));
    }

    public function cancel(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (! $order->is_cancellable) {
            return back()->with('error', __('app.order_cannot_be_cancelled'));
        }

        $order->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', __('app.order_cancelled_success'));
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items', 'invoice')
            ->firstOrFail();

        return view('shop.orders.show', compact('order'));
    }

    public function reorder(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        $added = 0;

        foreach ($order->items as $item) {
            if (! $item->product || ! $item->product->is_active) {
                continue;
            }

            CartItem::updateOrCreate(
                [
                    'user_id'    => Auth::id(),
                    'product_id' => $item->product_id,
                    'type'       => 'cart',
                ],
                [
                    'quantity' => DB::raw('quantity + ' . (int) $item->quantity),
                ]
            );
            $added++;
        }

        if ($added === 0) {
            return back()->with('error', __('app.no_products_found'));
        }

        return redirect()->route('cart.index')
            ->with('success', __('app.reorder_success'));
    }
}
