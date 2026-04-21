<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusUpdatedNotification;
use App\Notifications\ReviewRequestNotification;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('order_number', 'like', "%$s%")
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

        return view('admin.orders.index', compact('orders'));
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('user', 'items.product', 'invoice')
            ->firstOrFail();

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, string $orderNumber)
    {
        $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
        ]);

        $order          = Order::where('order_number', $orderNumber)->with('user')->firstOrFail();
        $previousStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Notify customer on meaningful status transitions
        $notifyStatuses = ['confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (in_array($request->status, $notifyStatuses) && $request->status !== $previousStatus) {
            $order->user->notify(new OrderStatusUpdatedNotification($order, $previousStatus));
        }

        // Send review request when order is delivered
        if ($request->status === 'delivered' && $previousStatus !== 'delivered') {
            $order->load('items.product');
            $order->user->notify(new ReviewRequestNotification($order));
        }

        return back()->with('success', "Estat de la comanda actualitzat a «{$request->status}». ✅");
    }

    public function updateTracking(Request $request, string $orderNumber)
    {
        $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'tracking_url'    => ['nullable', 'url', 'max:500'],
            'admin_notes'     => ['nullable', 'string', 'max:2000'],
        ]);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $order->update($request->only('tracking_number', 'tracking_url', 'admin_notes'));

        return back()->with('success', 'Informació d\'enviament actualitzada. ✅');
    }

    public function markPaid(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $order->update([
            'payment_status'       => 'paid',
            'payment_confirmed_at' => now(),
        ]);

        return back()->with('success', 'Pagament confirmat. ✅');
    }
}
