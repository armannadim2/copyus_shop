<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class B2BApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->role !== 'approved') {
            abort(403, 'Accés denegat.');
        }

        if (! $user->is_active) {
            abort(403, 'Compte desactivat.');
        }

        return $next($request);
    }
}
