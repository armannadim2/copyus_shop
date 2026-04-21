<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        $available = config('app.available_locales', ['ca', 'es', 'en']);

        if (in_array($locale, $available)) {
            Session::put('locale', $locale);
        }

        return redirect()->back()->withInput();
    }
}
