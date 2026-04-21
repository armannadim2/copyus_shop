<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class AdminPromoCodeController extends Controller
{
    public function index()
    {
        $codes = PromoCode::latest()->paginate(20);
        return view('admin.promo_codes.index', compact('codes'));
    }

    public function create()
    {
        return view('admin.promo_codes.form', ['code' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        PromoCode::create($data);
        return redirect()->route('admin.promo-codes.index')->with('success', 'Codi creat correctament.');
    }

    public function edit(int $id)
    {
        $code = PromoCode::findOrFail($id);
        return view('admin.promo_codes.form', compact('code'));
    }

    public function update(Request $request, int $id)
    {
        $data = $this->validated($request, $id);
        PromoCode::findOrFail($id)->update($data);
        return redirect()->route('admin.promo-codes.index')->with('success', 'Codi actualitzat.');
    }

    public function destroy(int $id)
    {
        PromoCode::findOrFail($id)->delete();
        return back()->with('success', 'Codi eliminat.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code'               => ['required', 'string', 'max:50', "unique:promo_codes,code,{$ignoreId}"],
            'description'        => ['nullable', 'string', 'max:255'],
            'type'               => ['required', 'in:percent,fixed'],
            'value'              => ['required', 'numeric', 'min:0'],
            'min_order_total'    => ['nullable', 'numeric', 'min:0'],
            'max_uses'           => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user'  => ['nullable', 'integer', 'min:1'],
            'valid_from'         => ['nullable', 'date'],
            'valid_until'        => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active'          => ['boolean'],
        ]);
    }
}
