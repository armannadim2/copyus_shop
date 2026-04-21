<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureB2BApproved
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isPending()) {
            return redirect()->route('approval.pending');
        }

        if ($user->isRejected()) {
            return redirect()->route('approval.rejected');
        }

        if ($user->isApproved()) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
