<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class AdminPromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $allowed = ['code', 'value', 'used_count', 'is_active', 'valid_from', 'created_at'];
        $sort    = in_array($request->input('sort'), $allowed) ? $request->input('sort') : 'created_at';
        $dir     = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $codes = PromoCode::orderBy($sort, $dir)->paginate(20)->withQueryString();
        return view('admin.promo_codes.index', compact('codes', 'sort', 'dir'));
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
