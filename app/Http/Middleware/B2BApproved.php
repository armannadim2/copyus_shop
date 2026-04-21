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

        if ($user->role === 'pending') {
            return redirect()->route('pending');
        }

        if ($user->role === 'rejected') {
            return redirect()->route('rejected');
        }

        if (!in_array($user->role, ['approved', 'admin'])) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Compte no autoritzat.');
        }

        return $next($request);
    }
}
