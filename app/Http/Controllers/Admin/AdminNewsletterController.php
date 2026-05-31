<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminNewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscription::latest();

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $subscriptions = $query->paginate(30)->withQueryString();

        $stats = [
            'total_active'   => NewsletterSubscription::active()->count(),
            'total_all'      => NewsletterSubscription::count(),
            'this_month'     => NewsletterSubscription::active()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('admin.newsletter.index', compact('subscriptions', 'stats'));
    }

    public function destroy(NewsletterSubscription $newsletterSubscription)
    {
        $newsletterSubscription->delete();

        return back()->with('success', 'Subscripció eliminada.');
    }

    public function export(): StreamedResponse
    {
        $subscriptions = NewsletterSubscription::active()->latest()->get();

        return response()->streamDownload(function () use ($subscriptions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Data subscripció', 'IP', 'Actiu']);
            foreach ($subscriptions as $sub) {
                fputcsv($handle, [
                    $sub->email,
                    $sub->created_at->format('d/m/Y H:i'),
                    $sub->ip_address ?? '',
                    $sub->is_active ? 'Sí' : 'No',
                ]);
            }
            fclose($handle);
        }, 'subscriptors_' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
