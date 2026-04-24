<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::withCount('members')
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('cif_vat', 'like', "%$s%")
            )
            ->when($request->filled('active'), fn($q) =>
                $q->where('is_active', $request->active === '1')
            )
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['members' => fn($q) => $q->orderBy('company_role')]);

        $orders = \App\Models\Order::whereIn('user_id', $company->members->pluck('id'))
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        $outstandingTotal = \App\Models\Invoice::whereHas('order', fn($q) =>
            $q->whereIn('user_id', $company->members->pluck('id'))
        )->where('payment_status', 'unpaid')
         ->sum('total_amount');

        return view('admin.companies.show', compact('company', 'orders', 'outstandingTotal'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'payment_terms'      => ['nullable', 'in:immediate,net_15,net_30,net_60,net_90'],
            'credit_limit'       => ['nullable', 'numeric', 'min:0'],
            'approval_threshold' => ['nullable', 'numeric', 'min:0'],
            'is_active'          => ['nullable', 'boolean'],
        ]);

        $payload = array_filter([
            'payment_terms'      => $request->input('payment_terms'),
            'credit_limit'       => $request->input('credit_limit'),
            'approval_threshold' => $request->input('approval_threshold'),
        ], fn($v) => $v !== null);

        if ($request->has('is_active')) {
            $payload['is_active'] = $request->boolean('is_active');
        }

        $company->update($payload);

        return back()->with('success', 'Condicions de l\'empresa actualitzades.');
    }
}
