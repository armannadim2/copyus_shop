<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class AdminQuoteRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = QuoteRequest::with('user')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        $counts = QuoteRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.quote_requests.index', compact('requests', 'counts'));
    }

    public function show(QuoteRequest $quoteRequest)
    {
        $quoteRequest->load('user');

        return view('admin.quote_requests.show', compact('quoteRequest'));
    }

    public function update(Request $request, QuoteRequest $quoteRequest)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:new,in_review,quoted,closed'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $quoteRequest->update($data);

        return back()->with('success', 'Sol·licitud actualitzada.');
    }

    public function destroy(QuoteRequest $quoteRequest)
    {
        $quoteRequest->delete();

        return redirect()->route('admin.quote-requests.index')
            ->with('success', 'Sol·licitud eliminada.');
    }
}
