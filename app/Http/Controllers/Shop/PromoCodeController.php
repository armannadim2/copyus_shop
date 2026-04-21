<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoCodeController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);

        $code = PromoCode::where('code', strtoupper(trim($request->code)))
            ->where('is_active', true)
            ->first();

        if (!$code) {
            return back()->with('promo_error', 'Codi de descompte no vàlid.');
        }

        $subtotal = CartItem::where('user_id', Auth::id())
            ->where('type', 'cart')
            ->with('product')
            ->get()
            ->sum(fn($i) => $i->product->price * $i->quantity);

        $valid = $code->isValid($subtotal, Auth::user());
        if ($valid !== true) {
            return back()->with('promo_error', $valid);
        }

        session(['promo_code' => $code->code]);

        return back()->with('promo_success', "Descompte «{$code->code}» aplicat correctament.");
    }

    public function remove()
    {
        session()->forget('promo_code');
        return back()->with('promo_success', 'Codi de descompte eliminat.');
    }
}
